<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkKycRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'pan',
        'dob',
        'status',
        'error_message',
        'kyc_record_id',
        'document_path',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    /**
     * Get the batch this record belongs to
     */
    public function batch()
    {
        return $this->belongsTo(BulkKycBatch::class, 'batch_id');
    }

    /**
     * Get the KYC record
     */
    public function kycRecord()
    {
        return $this->belongsTo(KycRecord::class, 'kyc_record_id');
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_SUCCESS => 'green',
            self::STATUS_PROCESSING => 'blue',
            self::STATUS_FAILED => 'red',
            default => 'yellow',
        };
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            self::STATUS_SUCCESS => 'Success',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_FAILED => 'Failed',
            default => 'Pending',
        };
    }

    /**
     * Scope for pending records
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for success records
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    /**
     * Scope for failed records
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }
}
