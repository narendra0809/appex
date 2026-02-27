<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Jobs\GenerateInvoicePdf;
use App\Jobs\SendDocumentEmail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentMail;

class InvoiceController extends Controller
{
    // Send dynamic invoice via email - Uses Queue
    public function sendEmail($clientId)
    {
        // Release session file lock so other requests aren't blocked
        session()->save();
        
        $client = Client::findOrFail($clientId);

        if (!$client->email) {
            return back()->with('error', 'Client email not found');
        }

        // Dispatch job to queue for PDF generation and email sending
        GenerateInvoicePdf::dispatch(
            $clientId,
            true, // sendEmail
            $client->email,
            'Invoice from Apex Capital Research',
            'Please find attached your invoice. For any queries, contact compliance@apexcapitalresearch.com',
            ['info@apexcapitalresearch.com']
        );

        return back()->with('success', 'Invoice email has been queued and will be sent shortly');
    }

    // Download dynamic invoice PDF - check if exists first
    public function word($clientId)
    {
        // Release session file lock so other requests aren't blocked
        session()->save();
        
        $client = Client::with('invoice')->findOrFail($clientId);

        try {
            // Check if PDF already exists in storage
            $path = storage_path('invoice_' . $client->id . '.pdf');
            
            if (file_exists($path)) {
                // Return existing file
                return response()->download($path, 'invoice_' . $client->id . '.pdf');
            }
            
            // Generate new PDF if not exists
            $pdf = Pdf::loadView('test', compact('client'));
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('isRemoteEnabled', true);
            $pdf->setOption('timeout', 120);
            $pdf->setOption('chroot', public_path());
            
            return $pdf->download('invoice_'.$client->id.'.pdf');
        } catch (\Exception $e) {
            \Log::error('Invoice PDF generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }

    // PDF (simple – not exact, optional)
    public function pdf($clientId)
    {
        // Release session file lock so other requests aren't blocked
        session()->save();
        
        $client = Client::findOrFail($clientId);

        try {
            $pdf = Pdf::loadView('invoice.invoice-pdf', compact('client'));
            return $pdf->download('invoice_'.$client->id.'.pdf');
        } catch (\Exception $e) {
            \Log::error('Invoice PDF generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }
}
