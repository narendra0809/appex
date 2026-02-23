<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use Webklex\PHPIMAP\Exceptions\FolderNotFoundException;
use Webklex\PHPIMAP\Exceptions\RuntimeException;

class MailAppendService
{
    /**
     * IMAP client instance.
     */
    protected ?Client $client = null;

    /**
     * Connection configuration.
     */
    protected array $config;

    /**
     * List of possible sent folder names to try.
     */
    protected array $sentFolderNames = [
        'Sent',
        'Sent Items',
        'Sent Messages',
        'INBOX.Sent',
        'INBOX.Sent Items',
    ];

    /**
     * Create a new MailAppendService instance.
     */
    public function __construct()
    {
        $this->config = [
            'host' => config('imap.connections.default.host', env('IMAP_HOST', 'imap.hostinger.com')),
            'port' => config('imap.connections.default.port', env('IMAP_PORT', 993)),
            'encryption' => config('imap.connections.default.encryption', env('IMAP_ENCRYPTION', 'ssl')),
            'validate_cert' => config('imap.connections.default.validate_cert', env('IMAP_VALIDATE_CERT', true)),
            'username' => config('imap.connections.default.username', env('IMAP_USERNAME', env('MAIL_USERNAME'))),
            'password' => config('imap.connections.default.password', env('IMAP_PASSWORD', env('MAIL_PASSWORD'))),
            'protocol' => config('imap.connections.default.protocol', env('IMAP_PROTOCOL', 'imap')),
        ];

        // Override with config sent folders if available
        if (config('imap.sent_folders')) {
            $this->sentFolderNames = config('imap.sent_folders');
        }
    }

    /**
     * Append a raw MIME message to the Sent folder.
     *
     * @param string $rawMessage The raw MIME message content
     * @param array $options Optional parameters (seen, flagged, etc.)
     * @return bool True if successful, false otherwise
     */
    public function appendToSent(string $rawMessage, array $options = []): bool
    {
        try {
            // Connect to IMAP
            $client = $this->connect();
            
            if (!$client) {
                Log::error('MailAppendService: Failed to connect to IMAP server');
                return false;
            }

            // Find the Sent folder
            $sentFolder = $this->findSentFolder($client);
            
            if (!$sentFolder) {
                Log::error('MailAppendService: Could not find Sent folder');
                return false;
            }

            // Append the message
            $this->appendMessage($sentFolder, $rawMessage, $options);

            // Disconnect from IMAP
            $client->disconnect();

            Log::info('MailAppendService: Message successfully appended to Sent folder');
            return true;

        } catch (ConnectionFailedException $e) {
            Log::error('MailAppendService: IMAP connection failed', [
                'error' => $e->getMessage(),
                'host' => $this->config['host'],
                'port' => $this->config['port'],
            ]);
            return false;
        } catch (FolderNotFoundException $e) {
            Log::error('MailAppendService: Sent folder not found', [
                'error' => $e->getMessage(),
                'tried_folders' => $this->sentFolderNames,
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('MailAppendService: Unexpected error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Connect to the IMAP server.
     *
     * @return Client|null
     */
    protected function connect(): ?Client
    {
        try {
            $cm = new ClientManager();
            $client = $cm->make($this->config);
            $client->connect();
            
            Log::debug('MailAppendService: Successfully connected to IMAP server', [
                'host' => $this->config['host'],
                'port' => $this->config['port'],
            ]);
            
            return $client;
        } catch (ConnectionFailedException $e) {
            Log::error('MailAppendService: Connection failed', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Find the Sent folder by trying different common names.
     *
     * @param Client $client
     * @return Folder|null
     */
    protected function findSentFolder(Client $client): ?Folder
    {
        // Get all folders
        $folders = $client->getFolders(false);
        
        Log::debug('MailAppendService: Available folders', [
            'folders' => $folders->map(fn($f) => $f->name)->toArray(),
        ]);

        // Try each possible sent folder name
        foreach ($this->sentFolderNames as $folderName) {
            try {
                $folder = $client->getFolderByName($folderName);
                if ($folder) {
                    Log::debug('MailAppendService: Found Sent folder', [
                        'folder' => $folderName,
                    ]);
                    return $folder;
                }
            } catch (\Exception $e) {
                // Continue to next folder name
                continue;
            }
        }

        // Try case-insensitive matching
        foreach ($folders as $folder) {
            $folderNameLower = strtolower($folder->name);
            foreach (['sent', 'sent items', 'sent messages'] as $sentName) {
                if (str_contains($folderNameLower, $sentName)) {
                    Log::debug('MailAppendService: Found Sent folder (case-insensitive)', [
                        'folder' => $folder->name,
                    ]);
                    return $folder;
                }
            }
        }

        return null;
    }

    /**
     * Append a raw message to a folder.
     *
     * @param Folder $folder
     * @param string $rawMessage
     * @param array $options
     * @return void
     */
    protected function appendMessage(Folder $folder, string $rawMessage, array $options = []): void
    {
        // Default options: mark as seen
        $flags = $options['flags'] ?? ['\\Seen'];
        
        // Use the IMAP append command
        $rawMessage = rtrim($rawMessage);
        
        // Build the append command
        $flagString = !empty($flags) ? '(' . implode(' ', $flags) . ')' : '';
        
        // Use PHP's IMAP functions directly for more control
        $imapStream = $this->getImapStream($folder);
        
        if ($imapStream) {
            $result = @imap_append(
                $imapStream,
                $this->getFolderMailboxName($folder),
                $rawMessage . "\r\n",
                $flagString
            );
            
            if (!$result) {
                $error = imap_last_error();
                Log::error('MailAppendService: imap_append failed', [
                    'error' => $error,
                    'folder' => $folder->name,
                ]);
                imap_errors(); // Clear errors
            }
        } else {
            // Fallback: try using the package's method
            $folder->appendMessage($rawMessage, $flags);
        }
    }

    /**
     * Get the raw IMAP stream resource.
     *
     * @param Folder $folder
     * @return resource|null
     */
    protected function getImapStream(Folder $folder)
    {
        // Access the underlying client connection
        $client = $folder->getClient();
        
        if ($client && method_exists($client, 'getConnection')) {
            $connection = $client->getConnection();
            if (is_resource($connection) || $connection instanceof \IMAP\Connection) {
                return $connection;
            }
        }
        
        return null;
    }

    /**
     * Get the full mailbox name for a folder.
     *
     * @param Folder $folder
     * @return string
     */
    protected function getFolderMailboxName(Folder $folder): string
    {
        $host = $this->config['host'];
        $port = $this->config['port'];
        $encryption = $this->config['encryption'];
        $username = $this->config['username'];
        
        // Build the mailbox string
        $mailbox = '{' . $host . ':' . $port . '/' . $encryption . '}';
        $mailbox .= $folder->name;
        
        return $mailbox;
    }

    /**
     * Build a raw MIME message from email components.
     *
     * @param string $fromEmail
     * @param string $fromName
     * @param string $toEmail
     * @param string $subject
     * @param string $body
     * @param array $ccEmails
     * @param array $attachments Array of ['path' => string, 'name' => string, 'mime' => string]
     * @return string
     */
    public function buildRawMessage(
        string $fromEmail,
        string $fromName,
        string $toEmail,
        string $subject,
        string $body,
        array $ccEmails = [],
        array $attachments = []
    ): string {
        $boundary = md5(time() . rand());
        $boundaryMixed = md5(time() . rand() . 'mixed');
        
        $lines = [];
        
        // Headers
        $lines[] = 'From: ' . $this->formatAddress($fromEmail, $fromName);
        $lines[] = 'To: ' . $toEmail;
        
        if (!empty($ccEmails)) {
            $lines[] = 'Cc: ' . implode(', ', $ccEmails);
        }
        
        $lines[] = 'Subject: ' . $this->encodeSubject($subject);
        $lines[] = 'Date: ' . date('r');
        $lines[] = 'Message-ID: <' . uniqid() . '@' . parse_url(config('app.url'), PHP_URL_HOST) . '>';
        $lines[] = 'MIME-Version: 1.0';
        
        // Check if we have attachments
        if (!empty($attachments)) {
            $lines[] = 'Content-Type: multipart/mixed; boundary="' . $boundaryMixed . '"';
            $lines[] = '';
            $lines[] = '--' . $boundaryMixed;
            $lines[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
            $lines[] = '';
        } else {
            $lines[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
            $lines[] = '';
        }
        
        // Plain text body
        $lines[] = '--' . $boundary;
        $lines[] = 'Content-Type: text/plain; charset=UTF-8';
        $lines[] = 'Content-Transfer-Encoding: quoted-printable';
        $lines[] = '';
        $lines[] = $this->quotedPrintableEncode($body);
        $lines[] = '';
        
        // HTML body
        $lines[] = '--' . $boundary;
        $lines[] = 'Content-Type: text/html; charset=UTF-8';
        $lines[] = 'Content-Transfer-Encoding: quoted-printable';
        $lines[] = '';
        $lines[] = $this->quotedPrintableEncode(nl2br(e($body)));
        $lines[] = '';
        
        $lines[] = '--' . $boundary . '--';
        
        // Attachments
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                if (isset($attachment['path']) && file_exists($attachment['path'])) {
                    $content = file_get_contents($attachment['path']);
                    $filename = $attachment['name'] ?? basename($attachment['path']);
                    $mime = $attachment['mime'] ?? 'application/octet-stream';
                    
                    $lines[] = '';
                    $lines[] = '--' . $boundaryMixed;
                    $lines[] = 'Content-Type: ' . $mime . '; name="' . $filename . '"';
                    $lines[] = 'Content-Transfer-Encoding: base64';
                    $lines[] = 'Content-Disposition: attachment; filename="' . $filename . '"';
                    $lines[] = '';
                    $lines[] = chunk_split(base64_encode($content));
                }
            }
            $lines[] = '--' . $boundaryMixed . '--';
        }
        
        return implode("\r\n", $lines);
    }

    /**
     * Format an email address with optional name.
     *
     * @param string $email
     * @param string|null $name
     * @return string
     */
    protected function formatAddress(string $email, ?string $name = null): string
    {
        if ($name) {
            return $this->encodeHeader($name) . ' <' . $email . '>';
        }
        return $email;
    }

    /**
     * Encode a subject line.
     *
     * @param string $subject
     * @return string
     */
    protected function encodeSubject(string $subject): string
    {
        if (preg_match('/[^\x20-\x7E]/', $subject)) {
            return '=?UTF-8?B?' . base64_encode($subject) . '?=';
        }
        return $subject;
    }

    /**
     * Encode a header value.
     *
     * @param string $value
     * @return string
     */
    protected function encodeHeader(string $value): string
    {
        if (preg_match('/[^\x20-\x7E]/', $value)) {
            return '=?UTF-8?B?' . base64_encode($value) . '?=';
        }
        return $value;
    }

    /**
     * Quoted-printable encode a string.
     *
     * @param string $string
     * @return string
     */
    protected function quotedPrintableEncode(string $string): string
    {
        return quoted_printable_encode($string);
    }

    /**
     * Test the IMAP connection.
     *
     * @return array
     */
    public function testConnection(): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'folders' => [],
        ];

        try {
            $client = $this->connect();
            
            if (!$client) {
                $result['message'] = 'Failed to connect to IMAP server';
                return $result;
            }

            $folders = $client->getFolders(false);
            $result['folders'] = $folders->map(fn($f) => $f->name)->toArray();
            
            $client->disconnect();
            
            $result['success'] = true;
            $result['message'] = 'Successfully connected to IMAP server';
            
            return $result;

        } catch (\Exception $e) {
            $result['message'] = 'Error: ' . $e->getMessage();
            return $result;
        }
    }
}
