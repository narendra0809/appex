<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'pan',
        'name',
        'dob',
        'father_name',
        'address',
        'pincode',
        'state',
        'city',
        'status',
        'kyc_status',
        'kyc_json',
        'verified_at',
        'document_path',
        'notes',
        // New fields for API
        'zip_path',
        'ref_no',
        'api_raw_response',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'kyc_json' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_NOT_FOUND = 'not_found';

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_VERIFIED => 'green',
            self::STATUS_NOT_FOUND => 'red',
            default => 'yellow',
        };
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_NOT_FOUND => 'Not Found',
            default => 'Pending',
        };
    }

    /**
     * Scope for verified records
     */
    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_VERIFIED);
    }

    /**
     * Scope for pending records
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Find by PAN
     */
    public static function findByPan(string $pan): ?self
    {
        return static::where('pan', strtoupper($pan))->first();
    }
}
