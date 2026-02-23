# Laravel Queue SYNC Driver - Complete Guide

## Overview

This guide explains how to use the `sync` queue driver in Laravel 11 for immediate job execution without any background workers, cron jobs, or supervisor processes.

## Why Use SYNC Driver?

### Perfect For:
- **Shared Hosting**: No SSH access required
- **Low Traffic Sites**: Emails sent immediately
- **Simple Setup**: No additional configuration
- **Development**: Easy debugging

### NOT Recommended For:
- High-volume email sending
- Long-running tasks (>30 seconds)
- Time-sensitive batch processing

## Configuration

### 1. .env Configuration

```env
# Queue Configuration
# sync = Immediate execution (no worker needed)
# database = Requires queue:work command
# redis = Requires Redis server + queue:work
QUEUE_CONNECTION=sync

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD="your-password"
MAIL_FROM_ADDRESS=your-email@yourdomain.com
MAIL_FROM_NAME="Your Company"

# IMAP Configuration (for Sent folder appending)
IMAP_HOST=imap.hostinger.com
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
IMAP_VALIDATE_CERT=true
IMAP_USERNAME="${MAIL_USERNAME}"
IMAP_PASSWORD="${MAIL_PASSWORD}"
```

### 2. config/queue.php Explanation

```php
<?php

return [
    /*
    | Default Queue Connection Name
    | 
    | This reads from .env: QUEUE_CONNECTION=sync
    | If not set, defaults to 'database'
    */
    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    | Queue Connections
    | 
    | Each driver has different behavior:
    */
    'connections' => [

        /*
        | SYNC Driver
        | -----------
        | - Executes job immediately within the same request
        | - No queue table needed
        | - No worker process needed
        | - Job runs synchronously (blocking)
        | - User waits until job completes
        */
        'sync' => [
            'driver' => 'sync',
        ],

        /*
        | DATABASE Driver
        | ---------------
        | - Stores jobs in 'jobs' table
        | - Requires: php artisan queue:work
        | - Requires: supervisor for production
        | - Non-blocking (user gets immediate response)
        | - Jobs processed in background
        */
        'database' => [
            'driver' => 'database',
            'connection' => env('DB_QUEUE_CONNECTION'),
            'table' => env('DB_QUEUE_TABLE', 'jobs'),
            'queue' => env('DB_QUEUE', 'default'),
            'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
            'after_commit' => false,
        ],

        /*
        | REDIS Driver
        | ------------
        | - Stores jobs in Redis memory
        | - Faster than database
        | - Requires: Redis server
        | - Requires: php artisan queue:work
        | - Best for high-volume applications
        */
        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 90),
            'block_for' => null,
            'after_commit' => false,
        ],
    ],
];
```

## Performance Comparison

| Feature | sync | database | redis |
|---------|------|----------|-------|
| **Execution** | Immediate | Background | Background |
| **Worker Required** | No | Yes | Yes |
| **Cron Required** | No | Yes* | Yes* |
| **Response Time** | Slower | Faster | Fastest |
| **Memory Usage** | Low | Medium | High |
| **Scalability** | Low | Medium | High |
| **Shared Hosting** | ✓ Perfect | Limited | No |
| **Setup Complexity** | Simple | Medium | Complex |

*Can use supervisor instead of cron

### Performance Impact

**SYNC Driver:**
```
Request Start → Job Dispatch → Job Execute → Request End
                     ↓
              User waits for completion
              
Total Time = Request Time + Job Execution Time
```

**DATABASE/REDIS Driver:**
```
Request Start → Job Dispatch → Request End
                     ↓
              Job queued for later
              
Total Time = Request Time only
Job processed separately by worker
```

## Job Class Example

### SendDocumentEmail.php

```php
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
use Symfony\Component\Mime\Email as SymfonyEmail;

class SendDocumentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $subject;
    public string $body;
    public string $filePath;
    public string $toEmail;
    public array $ccEmails;

    /**
     * Job timeout in seconds.
     * With sync driver, this affects PHP max_execution_time.
     */
    public int $timeout = 120;

    /**
     * Number of retry attempts.
     * With sync driver, retries happen immediately.
     */
    public int $tries = 3;

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
     * 
     * With QUEUE_CONNECTION=sync:
     * - This runs immediately when dispatch() is called
     * - No queue worker needed
     * - Execution is synchronous (blocking)
     */
    public function handle(): void
    {
        try {
            Log::info('SendDocumentEmail: Starting', [
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
            ]);

            // Append to IMAP Sent folder
            $this->appendToImapSentFolder($mailable);

        } catch (\Exception $e) {
            Log::error('SendDocumentEmail: Failed', [
                'error' => $e->getMessage(),
                'to' => $this->toEmail,
                'subject' => $this->subject,
            ]);
            
            throw $e;
        }
    }

    /**
     * Append email to IMAP Sent folder.
     */
    protected function appendToImapSentFolder(DocumentMail $mailable): void
    {
        try {
            $mailAppendService = new MailAppendService();
            
            // Build raw MIME message
            $rawMessage = $this->buildRawMimeMessage($mailable);

            if (empty($rawMessage)) {
                Log::warning('SendDocumentEmail: Could not build MIME message');
                return;
            }

            $success = $mailAppendService->appendToSent($rawMessage);

            if ($success) {
                Log::info('SendDocumentEmail: Appended to IMAP Sent folder');
            } else {
                Log::warning('SendDocumentEmail: IMAP append failed (SMTP was successful)');
            }

        } catch (\Exception $e) {
            // Don't fail the job - SMTP was successful
            Log::error('SendDocumentEmail: IMAP error', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Build raw MIME message.
     */
    protected function buildRawMimeMessage(DocumentMail $mailable): ?string
    {
        try {
            $email = new SymfonyEmail();
            $email->from(new \Symfony\Component\Mime\Address(
                config('mail.from.address'),
                config('mail.from.name')
            ));
            $email->to($this->toEmail);
            
            if (!empty($this->ccEmails)) {
                foreach ($this->ccEmails as $ccEmail) {
                    $email->addCc($ccEmail);
                }
            }
            
            $email->subject($this->subject);
            $email->html($mailable->render());
            
            if (file_exists($this->filePath)) {
                $email->attachFromPath($this->filePath, basename($this->filePath));
            }
            
            return $email->toString();

        } catch (\Exception $e) {
            Log::error('SendDocumentEmail: MIME build error', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Handle job failure.
     * 
     * With sync driver, this is called immediately on exception.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendDocumentEmail: Job failed', [
            'error' => $exception->getMessage(),
            'to' => $this->toEmail,
            'subject' => $this->subject,
        ]);
    }
}
```

## Event Trigger Examples

### Controller Example

```php
<?php

namespace App\Http\Controllers;

use App\Jobs\SendDocumentEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('attachments');
            $filePath = storage_path('app/' . $filePath);
        }

        try {
            // Release session lock before long operation
            // This prevents session blocking
            session()->save();

            // With QUEUE_CONNECTION=sync, this executes immediately
            // No worker needed - email sends right now
            SendDocumentEmail::dispatch(
                $validated['email'],
                $validated['subject'],
                $validated['body'],
                $filePath ?? '',
                ['info@yourdomain.com'] // CC emails
            );

            return back()->with('success', 'Email sent successfully!');

        } catch (\Exception $e) {
            Log::error('Email send failed: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}
```

### Model Event Example

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\SendDocumentEmail;

class Invoice extends Model
{
    protected static function booted()
    {
        // Send email when invoice is created
        static::created(function (Invoice $invoice) {
            // With sync driver, email sends immediately
            SendDocumentEmail::dispatch(
                $invoice->client_email,
                "Invoice #{$invoice->number}",
                "Please find attached invoice #{$invoice->number}",
                $invoice->pdf_path,
                []
            );
        });
    }
}
```

### Event Listener Example

```php
<?php

namespace App\Listeners;

use App\Events\DocumentCreated;
use App\Jobs\SendDocumentEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendDocumentNotification implements ShouldQueue
{
    public function handle(DocumentCreated $event): void
    {
        // With sync driver, this runs immediately
        SendDocumentEmail::dispatch(
            $event->document->recipient_email,
            'New Document Available',
            'A new document has been created for you.',
            $event->document->file_path
        );
    }
}
```

## Best Practices for Production

### 1. Session Handling

```php
// Before dispatching, release session lock
session()->save();

SendDocumentEmail::dispatch(...);
```

### 2. Error Handling

```php
try {
    SendDocumentEmail::dispatch(...);
    return back()->with('success', 'Email sent!');
} catch (\Exception $e) {
    Log::error('Email failed: ' . $e->getMessage());
    return back()->with('error', 'Failed to send email');
}
```

### 3. Timeout Configuration

```php
// In Job class
public int $timeout = 120; // 2 minutes

// Ensure PHP max_execution_time is higher
// In php.ini or .htaccess:
// max_execution_time = 180
```

### 4. Memory Management

```php
// In Job class
public function __destruct()
{
    // Clean up large variables
    $this->filePath = null;
    gc_collect_cycles();
}
```

### 5. Logging

```php
// In Job class
public function handle(): void
{
    Log::info('Job started', ['to' => $this->toEmail]);
    
    try {
        // Job logic
        Log::info('Job completed', ['to' => $this->toEmail]);
    } catch (\Exception $e) {
        Log::error('Job failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        throw $e;
    }
}
```

## Troubleshooting

### Job Not Running

1. Check `.env`: `QUEUE_CONNECTION=sync`
2. Clear config cache: `php artisan config:clear`
3. Check logs: `storage/logs/laravel.log`

### Timeout Errors

1. Increase PHP `max_execution_time`
2. Reduce job `$timeout` value
3. Optimize email sending process

### Memory Errors

1. Clean up variables in `__destruct()`
2. Use `gc_collect_cycles()`
3. Process smaller batches

## Summary

| Setting | Value |
|---------|-------|
| Queue Driver | `sync` |
| Worker Required | No |
| Cron Required | No |
| Execution | Immediate |
| Best For | Shared Hosting |

With `QUEUE_CONNECTION=sync`, your jobs will execute immediately when dispatched, with no additional setup required. Perfect for shared hosting environments!
