<?php

namespace App\Services;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceWordService
{
    public function generate($client)
    {
        return $this->generatePdf($client);
    }
    
    private function generatePdf($client)
    {
        // Get values from database
        $net = (float) ($client->net_amount ?? 0);
        $gross = (float) ($client->gross_amount ?? 0);
        $gst = $gross - $net;
        
        \Log::info("Invoice PDF generation - net: $net, gross: $gross, gst: $gst");

        try {
            // Generate PDF from blade template
            $pdf = Pdf::loadView('test', compact('client'));
            $pdf->setPaper('a4', 'portrait');
            
            // Set options for remote images and timeouts
            $pdf->setOption('isRemoteEnabled', true);
            $pdf->setOption('timeout', 30);
            
            // Save the PDF
            $path = storage_path('invoice_' . $client->id . '_' . time() . '.pdf');
            $pdf->save($path);
            
            return $path;
        } catch (\Exception $e) {
            \Log::error("Invoice PDF generation failed: " . $e->getMessage());
            throw $e;
        }
    }
}
