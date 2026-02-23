<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Client;
use App\Models\Agreement;

class GenerateAgreementPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $clientId;
    public bool $sendEmail;
    public string $email;
    public string $subject;
    public string $body;
    public array $ccEmails;
    public bool $generateWord;

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
        array $ccEmails = [],
        bool $generateWord = false
    ) {
        $this->clientId = $clientId;
        $this->sendEmail = $sendEmail;
        $this->email = $email;
        $this->subject = $subject;
        $this->body = $body;
        $this->ccEmails = $ccEmails;
        $this->generateWord = $generateWord;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = Client::findOrFail($this->clientId);
        
        \Log::info("Agreement PDF generation job - client: {$client->id}");

        try {
            // Find or create agreement record
            $agreement = Agreement::firstOrNew(['client_id' => $this->clientId]);
            if (!$agreement->agreement_no) {
                $agreement->agreement_no = 'AGR-' . date('Ymd') . '-' . str_pad($this->clientId, 4, '0', STR_PAD_LEFT);
            }
            $agreement->agreement_sent_at = now();
            $agreement->save();

            // Get the agreement file path
            $filePath = public_path('agreement.docx');
            
            if (!file_exists($filePath)) {
                throw new \RuntimeException('Agreement file not found in public folder');
            }

            // If email flag is set, dispatch email job with the agreement file
            if ($this->sendEmail && $this->email) {
                SendDocumentEmail::dispatch(
                    $this->email,
                    $this->subject,
                    $this->body,
                    $filePath,
                    $this->ccEmails
                );
            }
        } catch (\Exception $e) {
            \Log::error("Agreement PDF generation job failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('GenerateAgreementPdf job failed: ' . $exception->getMessage());
    }
}
