<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\DocumentMail;
use App\Services\MailAppendService;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email as SymfonyEmail;
use Symfony\Component\Mime\RawMessage;

class SendDocumentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $subject;
    public string $body;
    public string $filePath;
    public string $toEmail;
    public array $ccEmails;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $toEmail,
        string $subject,
        string $body,
        string $filePath,
        array $ccEmails = []
    ) {
        $this->toEmail = $toEmail;
        $this->subject = $subject;
        $this->body = $body;
        $this->filePath = $filePath;
        $this->ccEmails = $ccEmails;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('SendDocumentEmail: Starting email send', [
                'to' => $this->toEmail,
                'subject' => $this->subject,
            ]);

            // Create the mailable instance
            $mailable = new DocumentMail(
                $this->subject,
                $this->body,
                $this->filePath
            );

            // Send via SMTP
            $mail = Mail::to($this->toEmail);

            if (!empty($this->ccEmails)) {
                $mail->cc($this->ccEmails);
            }

            $mail->send($mailable);

            Log::info('SendDocumentEmail: SMTP send successful', [
                'to' => $this->toEmail,
                'subject' => $this->subject,
            ]);

            // After successful SMTP send, append to IMAP Sent folder
            $this->appendToImapSentFolder($mailable);

        } catch (\Exception $e) {
            Log::error('SendDocumentEmail: Error during email send', [
                'error' => $e->getMessage(),
                'to' => $this->toEmail,
                'subject' => $this->subject,
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Re-throw to trigger failed() method
            throw $e;
        }
    }

    /**
     * Append the sent email to IMAP Sent folder.
     *
     * @param DocumentMail $mailable
     * @return void
     */
    protected function appendToImapSentFolder(DocumentMail $mailable): void
    {
        try {
            // Build the raw MIME message
            $rawMessage = $this->buildRawMimeMessage($mailable);

            if (empty($rawMessage)) {
                Log::warning('SendDocumentEmail: Could not build raw MIME message for IMAP append');
                return;
            }

            // Use MailAppendService to append to Sent folder
            $mailAppendService = new MailAppendService();
            $success = $mailAppendService->appendToSent($rawMessage);

            if ($success) {
                Log::info('SendDocumentEmail: Email appended to IMAP Sent folder', [
                    'to' => $this->toEmail,
                    'subject' => $this->subject,
                ]);
            } else {
                Log::warning('SendDocumentEmail: Failed to append email to IMAP Sent folder (SMTP send was successful)', [
                    'to' => $this->toEmail,
                    'subject' => $this->subject,
                ]);
            }

        } catch (\Exception $e) {
            // Log the error but don't fail the job - SMTP send was successful
            Log::error('SendDocumentEmail: IMAP append error (SMTP send was successful)', [
                'error' => $e->getMessage(),
                'to' => $this->toEmail,
                'subject' => $this->subject,
            ]);
        }
    }

    /**
     * Build the raw MIME message from the mailable.
     *
     * @param DocumentMail $mailable
     * @return string|null
     */
    protected function buildRawMimeMessage(DocumentMail $mailable): ?string
    {
        try {
            // Method 1: Try to get raw content from Symfony Email
            $symfonyEmail = $this->createSymfonyEmail($mailable);
            
            if ($symfonyEmail) {
                return $symfonyEmail->toString();
            }

            // Method 2: Build manually using MailAppendService
            $mailAppendService = new MailAppendService();
            
            $attachments = [];
            if (file_exists($this->filePath)) {
                $attachments[] = [
                    'path' => $this->filePath,
                    'name' => basename($this->filePath),
                    'mime' => $this->getMimeType($this->filePath),
                ];
            }

            return $mailAppendService->buildRawMessage(
                config('mail.from.address'),
                config('mail.from.name'),
                $this->toEmail,
                $this->subject,
                $this->body,
                $this->ccEmails,
                $attachments
            );

        } catch (\Exception $e) {
            Log::error('SendDocumentEmail: Error building raw MIME message', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Create a Symfony Email instance from the mailable.
     *
     * @param DocumentMail $mailable
     * @return SymfonyEmail|null
     */
    protected function createSymfonyEmail(DocumentMail $mailable): ?SymfonyEmail
    {
        try {
            // Render the mailable to get the content
            $rendered = $mailable->render();
            
            // Create Symfony Email
            $email = new SymfonyEmail();
            
            // Set from
            $email->from(new \Symfony\Component\Mime\Address(
                config('mail.from.address'),
                config('mail.from.name')
            ));
            
            // Set to
            $email->to($this->toEmail);
            
            // Set CC
            if (!empty($this->ccEmails)) {
                foreach ($this->ccEmails as $ccEmail) {
                    $email->addCc($ccEmail);
                }
            }
            
            // Set subject
            $email->subject($this->subject);
            
            // Set body
            $email->html($rendered);
            
            // Add attachment
            if (file_exists($this->filePath)) {
                $email->attachFromPath($this->filePath, basename($this->filePath));
            }
            
            return $email;

        } catch (\Exception $e) {
            Log::error('SendDocumentEmail: Error creating Symfony Email', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get the MIME type of a file.
     *
     * @param string $filePath
     * @return string
     */
    protected function getMimeType(string $filePath): string
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'zip' => 'application/zip',
        ];

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendDocumentEmail job failed: ' . $exception->getMessage(), [
            'to' => $this->toEmail,
            'subject' => $this->subject,
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
