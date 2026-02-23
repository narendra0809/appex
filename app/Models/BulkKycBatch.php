<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkKycBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_name',
        'original_filename',
        'status',
        'total_records',
        'processed_records',
        'success_count',
        'failed_count',
        'error_log',
        'result_zip_path',
        'started_at',
        'completed_at',
        'user_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * Get the records for this batch
     */
    public function records()
    {
        return $this->hasMany(BulkKycRecord::class, 'batch_id');
    }

    /**
     * Get the user who created this batch
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_COMPLETED => 'green',
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
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_FAILED => 'Failed',
            default => 'Pending',
        };
    }

    /**
     * Get progress percentage
     */
    public function getProgressAttribute(): int
    {
        if ($this->total_records === 0) {
            return 0;
        }
        return (int) round(($this->processed_records / $this->total_records) * 100);
    }

    /**
     * Scope for pending batches
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for processing batches
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    /**
     * Scope for completed batches
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
