<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    protected $fillable = [
        'client_id',
        'agreement_no',
        'pdf_path',
        'word_path',
        'agreement_sent_at',
        'invoice_sent_at',
    ];

    protected $casts = [
        'agreement_sent_at' => 'datetime',
        'invoice_sent_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
