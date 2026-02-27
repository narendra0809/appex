<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'client_id',
        'invoice_no',
        'pdf_path',
        'word_path',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the next auto-increment invoice number (for preview purposes)
     */
    public static function getNextInvoiceNumber(): string
    {
        $startNumber = 600;
        $prefix = 'ARC';
        
        // Get the last invoice number from database
        $lastInvoice = self::where('invoice_no', 'like', $prefix . '%')
            ->orderBy('invoice_no', 'desc')
            ->first();
        
        if ($lastInvoice && preg_match('/^ARC(\d+)$/', $lastInvoice->invoice_no, $matches)) {
            $lastNumber = (int) $matches[1];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = $startNumber;
        }
        
        return $prefix . $newNumber;
    }
}
