<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Agreement;
use App\Jobs\GenerateAgreementPdf;
use App\Jobs\SendDocumentEmail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentMail;

class AgreementController extends Controller
{
    // Get static agreement file from public folder
    public function getStaticAgreement()
    {
        // Release session lock before file download
        session()->save();
        
        $filePath = public_path('agreement.docx');
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Agreement file not found in public folder');
        }
        
        return response()->download($filePath, 'agreement.docx');
    }

    // Send static agreement via email - Uses Queue
    public function sendEmail($clientId)
    {
        // Release session lock before returning response
        session()->save();
        
        $client = Client::findOrFail($clientId);

        if (!$client->email) {
            return back()->with('error', 'Client email not found');
        }

        $filePath = public_path('agreement.docx');
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Agreement file not found in public folder');
        }

        // Dispatch job to queue for sending email
        SendDocumentEmail::dispatch(
            $client->email,
            'Agreement from Apex Capital Research',
            'Please find attached your agreement. For any queries, contact compliance@apexcapitalresearch.com',
            $filePath,
            ['info@apexcapitalresearch.com']
        );

        // Update agreement record
        $agreement = Agreement::firstOrNew(['client_id' => $clientId]);
        if (!$agreement->agreement_no) {
            $agreement->agreement_no = 'AGR-' . date('Ymd') . '-' . str_pad($clientId, 4, '0', STR_PAD_LEFT);
        }
        $agreement->agreement_sent_at = now();
        $agreement->save();

        return back()->with('success', 'Agreement email has been queued and will be sent shortly');
    }

    // WORD (using WordService for dynamic - if needed in future)
    public function word($clientId)
    {
        // Release session lock before long-running operation
        session()->save();
        
        $client = Client::findOrFail($clientId);
        
        // Use the service to generate Word document
        $service = app(\App\Services\AgreementWordService::class);
        $path = $service->generate($client);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    // PDF (simple – optional)
    public function pdf($clientId)
    {
        // Release session lock before long-running operation
        session()->save();
        
        $client = Client::findOrFail($clientId);

        try {
            $pdf = Pdf::loadView('agreement.agreement-pdf', compact('client'));
            return $pdf->download('agreement_'.$client->id.'.pdf');
        } catch (\Exception $e) {
            \Log::error('Agreement PDF generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate agreement: ' . $e->getMessage());
        }
    }
}