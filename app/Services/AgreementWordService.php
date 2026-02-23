<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;

class AgreementWordService
{
    public function generate($client)
    {
        // Use template file if exists (replace only yellow highlighted values),
        // otherwise create from scratch.
        $templatePath = public_path('agreement.docx');
        
        if (file_exists($templatePath)) {
            return $this->generateFromTemplate($client, $templatePath);
        }
        
        return $this->generateFromScratch($client);
    }
    
    private function generateFromTemplate($client, $templatePath)
    {
        $payment = $client->payment_date ? strtotime($client->payment_date) : time();
        $day = date('d', $payment);
        $month = date('F', $payment);
        $year = date('Y', $payment);

        $serviceStart = $client->service_start ? date('d M, Y', strtotime($client->service_start)) : '';
        $serviceEnd = $client->service_end ? date('d M, Y', strtotime($client->service_end)) : '';

        $address = trim(($client->city ?? '') . (empty($client->state) ? '' : ', ' . $client->state));

        // Yellow-highlight group index mapping (based on current `public/agreement.docx`)
        // 0 day blank (____)
        // 1 month+year line (starts with "of ...")
        // 2 [Client Name]
        // 3 address line underscores
        // 4 fee type underscores
        // 5 amount underscores
        // 6 duration underscores
        // 7 "the  unless" (we replace with duration phrase ending in "unless")
        // 8 signature line (leave)
        // 9 signature line (leave)
        // 10 name underscores (we can fill client name)
        // 11 Date:  (we fill with date)
        // 12 Place: (we fill with place)
        $replacements = [
            0  => $day,
            1  => 'of ' . $month . ', ' . $year,
            2  => (string) ($client->client_name ?? ''),
            3  => $address,
            4  => (string) ($client->plan ?? ''),
            5  => number_format((float) ($client->gross_amount ?? 0), 0),
            6  => trim($serviceStart . ' to ' . $serviceEnd),
            7  => 'from ' . $serviceStart . ' to ' . $serviceEnd . ' unless',
            10 => (string) ($client->client_name ?? ''),
            11 => 'Date: ' . date('d M, Y'),
            12 => 'Place: Udaipur',
        ];

        $path = storage_path('agreement_' . $client->id . '_' . time() . '.docx');
        return app(DocxYellowReplaceService::class)->generate($templatePath, $replacements, $path);
    }
    
    private function generateFromScratch($client)
    {
        $phpWord = new PhpWord();

        /* =========================
         | PAGE SETUP (A4 – WORD)
         ========================= */
        $section = $phpWord->addSection([
            'paperSize'    => 'A4',
            'marginTop'    => 1134, // 2 cm
            'marginBottom' => 1134,
            'marginLeft'   => 1134,
            'marginRight'  => 1134,
        ]);

        /* =========================
         | FONT STYLES
         ========================= */
        $phpWord->addFontStyle('normal', [
            'name' => 'Times New Roman',
            'size' => 11,
        ]);

        $phpWord->addFontStyle('bold', [
            'name' => 'Times New Roman',
            'size' => 11,
            'bold' => true,
        ]);

        $phpWord->addParagraphStyle('center', [
            'alignment' => Jc::CENTER,
        ]);

        /* =========================
         | TITLE
         ========================= */
        $section->addText('CLIENT AGREEMENT', 'bold', 'center');
        $section->addTextBreak(1);

        /* =========================
         | INTRO
         ========================= */
        $section->addText(
            'This Agreement is made on ' .
            date('d M, Y', strtotime($client->payment_date)),
            'normal'
        );

        $section->addTextBreak(1);

        $section->addText('By and Between:', 'bold');
        $section->addTextBreak(1);

        /* =========================
         | RA DETAILS
         ========================= */
        $section->addText(
            'KASHISH JOSHI, a SEBI-registered Research Analyst ' .
            '(Registration No. INH000017240), having its principal place of ' .
            'business at Udaipur (hereinafter referred to as the “Research Analyst” or “RA”).',
            'normal'
        );

        $section->addTextBreak(1);

        /* =========================
         | CLIENT DETAILS
         ========================= */
        $section->addText(
            'AND',
            'bold'
        );
        $section->addTextBreak(1);

        $section->addText(
            $client->client_name .
            ', residing at ' .
            $client->city . ', ' . $client->state .
            ' (hereinafter referred to as the “Client”).',
            'normal'
        );

        $section->addTextBreak(1);

        $section->addText(
            'The “RA” and “Client” may individually be referred to as a “Party” and collectively as the “Parties”.',
            'normal'
        );

        $section->addTextBreak(2);

        /* =========================
         | CLAUSE 1
         ========================= */
        $section->addText('1. Objective', 'bold');
        $section->addText(
            'The objective of this Agreement is to set out the terms and conditions for the provision of research services by the RA to the Client in accordance with SEBI (Research Analyst) Regulations, 2014 and amendments thereto.',
            'normal'
        );

        $section->addTextBreak(1);

        /* =========================
         | CLAUSE 2
         ========================= */
        $section->addText('2. SEBI Registration and Contact Details', 'bold');

        $table = $section->addTable([
            'borderSize'  => 6,
            'borderColor' => '000000',
            'width'       => 100,
            'unit'        => TblWidth::PERCENT,
        ]);

        $table->addRow();
        $table->addCell(5000)->addText('SEBI Registration Number', 'bold');
        $table->addCell(5000)->addText('INH000017240', 'normal');

        $table->addRow();
        $table->addCell(5000)->addText('Principal Officer', 'bold');
        $table->addCell(5000)->addText('Kashish Joshi', 'normal');

        $table->addRow();
        $table->addCell(5000)->addText('Contact Email', 'bold');
        $table->addCell(5000)->addText('info@apexcapitalresearch.com', 'normal');

        $table->addRow();
        $table->addCell(5000)->addText('Phone', 'bold');
        $table->addCell(5000)->addText('+91 9171718453', 'normal');

        $section->addTextBreak(2);

        /* =========================
         | SERVICE DETAILS
         ========================= */
        $section->addText('3. Service Details', 'bold');

        $section->addText(
            'Plan: ' . $client->plan,
            'normal'
        );
        $section->addText(
            'Segment: ' . $client->segment,
            'normal'
        );
        $section->addText(
            'Fees: Rs. ' . number_format($client->gross_amount, 2),
            'normal'
        );
        $section->addText(
            'Service Period: ' .
            date('d M, Y', strtotime($client->service_start)) .
            ' to ' .
            date('d M, Y', strtotime($client->service_end)),
            'normal'
        );

        $section->addTextBreak(2);

        /* =========================
         | DISCLAIMER
         ========================= */
        $section->addText('4. Disclaimer', 'bold');
        $section->addText(
            'The Client understands that investment in securities is subject to market risk. ' .
            'The RA does not guarantee any returns or profits.',
            'normal'
        );

        $section->addTextBreak(2);

        /* =========================
         | SIGNATURE
         ========================= */
        $section->addText('IN WITNESS WHEREOF, the Parties have signed this Agreement.', 'normal');

        $section->addTextBreak(3);

        $signTable = $section->addTable([
            'width' => 100,
            'unit'  => TblWidth::PERCENT,
        ]);

        $signTable->addRow();
        $signTable->addCell(5000)->addText('CLIENT', 'bold');
        $signTable->addCell(5000)->addText('RESEARCH ANALYST', 'bold');

        $signTable->addRow();
        $signTable->addCell(5000)->addText('Signature: __________', 'normal');
        $signTable->addCell(5000)->addText('Signature: __________', 'normal');

        $signTable->addRow();
        $signTable->addCell(5000)->addText('Name: ' . $client->client_name, 'normal');
        $signTable->addCell(5000)->addText('Name: Kashish Joshi', 'normal');

        $signTable->addRow();
        $signTable->addCell(5000)->addText('Date: ' . date('d M, Y'), 'normal');
        $signTable->addCell(5000)->addText('Place: Udaipur', 'normal');

        /* =========================
         | SAVE FILE
         ========================= */
        $path = storage_path('agreement_' . $client->id . '_' . time() . '.docx');
        IOFactory::createWriter($phpWord, 'Word2007')->save($path);

        return $path;
    }
}
