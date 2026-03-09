<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Disposal extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'disposal_code',
        'equipment_id',
        'method',
        'reason',
        'disposal_date',
        'disposal_value',
        'recipient_name',
        'recipient_contact',
        'documentation',
        'status',
        'remarks',
        'requested_by',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'disposal_date' => 'date',
        'disposal_value' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'disposal_code', 'equipment_id', 'method', 'reason',
                'disposal_date', 'status'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($disposal) {
            if (empty($disposal->disposal_code)) {
                $prefix = Setting::getValue('disposal_code_prefix', 'DSP');
                $lastDisposal = self::withTrashed()->orderBy('id', 'desc')->first();
                $nextId = $lastDisposal ? $lastDisposal->id + 1 : 1;
                $disposal->disposal_code = $prefix . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }

            if (auth()->check()) {
                $disposal->created_by = auth()->id();
                $disposal->requested_by = auth()->id();
            }
        });
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getMethodBadgeAttribute(): string
    {
        return match ($this->method) {
            'sale' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'donation' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'recycling' => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200',
            'destruction' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'trade_in' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending_approval' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }
}
