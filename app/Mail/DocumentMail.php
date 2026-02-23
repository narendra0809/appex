<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentMail extends Mailable
{
    use SerializesModels;

    public string $subjectText;
    public string $messageText;
    public string $filePath;

    public function __construct($subjectText, $messageText, $filePath)
    {
        $this->subjectText = $subjectText;
        $this->messageText = $messageText;
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($this->subjectText)
            ->view('emails.document')
            ->with(['messageText' => $this->messageText])
            ->attach($this->filePath);
    }
}
