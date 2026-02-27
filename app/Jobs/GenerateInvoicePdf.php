<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Client;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateInvoicePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $clientId;
    public bool $sendEmail;
    public string $email;
    public string $subject;
    public string $body;
    public array $ccEmails;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        int $clientId,
        bool $sendEmail = false,
        string $email = '',
        string $subject = '',
        string $body = '',
        array $ccEmails = []
    ) {
        $this->clientId = $clientId;
        $this->sendEmail = $sendEmail;
        $this->email = $email;
        $this->subject = $subject;
        $this->body = $body;
        $this->ccEmails = $ccEmails;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = Client::with('invoice')->findOrFail($this->clientId);
        
        // Get values from database
        $net = (float) ($client->net_amount ?? 0);
        $gross = (float) ($client->gross_amount ?? 0);
        
        \Log::info("Invoice PDF generation job - client: {$client->id}, net: $net, gross: $gross");

        try {
            // Check if invoice was already generated and sent
            $invoice = Invoice::where('client_id', $this->clientId)->first();
            $existingPath = null;
            
            // Check if PDF already exists in storage folder - simple filename without timestamp
            $path = storage_path('invoice_' . $client->id . '.pdf');
            if (file_exists($path)) {
                $existingPath = $path;
            }
            
            // If invoice was already sent and PDF exists, skip regeneration
            if ($invoice && $invoice->sent_at && $existingPath && file_exists($existingPath)) {
                \Log::info("Invoice PDF already exists for client {$client->id}, using existing file: $existingPath");
                $path = $existingPath;
            } else {
                // Generate new PDF
                $pdf = Pdf::loadView('test', compact('client'));
                $pdf->setPaper('a4', 'portrait');
                
                // Set options for remote images and timeouts
                $pdf->setOption('isRemoteEnabled', true);
                $pdf->setOption('timeout', 120);
                
                // Save the PDF - simple filename without timestamp
                $path = storage_path('invoice_' . $client->id . '.pdf');
                $pdf->save($path);
                \Log::info("New Invoice PDF generated for client {$client->id}: $path");
            }

            // Find or create invoice record
            if (!$invoice) {
                $invoice = new Invoice();
                $invoice->client_id = $this->clientId;
            }
            if (!$invoice->invoice_no) {
                // Generate auto-increment invoice number starting from ARC600
                $invoice->invoice_no = $this->generateInvoiceNumber();
            }
            $invoice->sent_at = now();
            $invoice->save();

            // If email flag is set, dispatch email job
            if ($this->sendEmail && $this->email) {
                SendDocumentEmail::dispatch(
                    $this->email,
                    $this->subject,
                    $this->body,
                    $path,
                    $this->ccEmails
                );
            }
        } catch (\Exception $e) {
            \Log::error("Invoice PDF generation job failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('GenerateInvoicePdf job failed: ' . $exception->getMessage());
    }

    /**
     * Generate auto-increment invoice number starting from ARC600
     */
    private function generateInvoiceNumber(): string
    {
        // Starting number
        $startNumber = 600;
        $prefix = 'ARC';
        
        // Get the last invoice number from database
        $lastInvoice = Invoice::where('invoice_no', 'like', $prefix . '%')
            ->orderBy('invoice_no', 'desc')
            ->first();
        
        if ($lastInvoice && preg_match('/^ARC(\d+)$/', $lastInvoice->invoice_no, $matches)) {
            // Extract the numeric part and increment
            $lastNumber = (int) $matches[1];
            $newNumber = $lastNumber + 1;
        } else {
            // No previous invoice, start from ARC600
            $newNumber = $startNumber;
        }
        
        return $prefix . $newNumber;
    }
}
