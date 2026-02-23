<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'payment_date',
        'client_name',
        'mobile',
        'email',
        'father_name',
        'pan_card',
        'aadhaar_card',
        'dob',
        'city',
        'state',
        'gross_amount',
        'net_amount',
        'amount_type',
        'segment',
        'bank',
        'remark',
        'assigned_to',
        'plan',
        'service_start',
        'service_end',
    ];

    public function agreement()
    {
        return $this->hasOne(Agreement::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
