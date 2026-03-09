<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'transaction_code',
        'equipment_id',
        'type',
        'person_firstname',
        'person_lastname',
        'person_middlename',
        'department_id',
        'contact_number',
        'email',
        'transaction_date',
        'expected_return_date',
        'actual_return_date',
        'purpose',
        'remarks',
        'status',
        'processed_by',
        'processed_at',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'processed_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'transaction_code', 'equipment_id', 'type', 'person_firstname',
                'person_lastname', 'transaction_date', 'status'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_code)) {
                $prefix = Setting::getValue('transaction_code_prefix', 'TRX');
                $dateCode = now()->format('Y-m-d');
                
                // Count transactions for today to generate sequence number
                $todayCount = self::withTrashed()
                    ->whereDate('created_at', now()->toDateString())
                    ->count();
                $sequence = str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);
                
                $transaction->transaction_code = $prefix . '-' . $dateCode . '-' . $sequence;
            }

            if (auth()->check()) {
                $transaction->created_by = auth()->id();
            }
        });
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPersonFullNameAttribute(): string
    {
        return trim("{$this->person_firstname} {$this->person_middlename} {$this->person_lastname}");
    }

    // Alias for person_full_name
    public function getPersonNameAttribute(): string
    {
        return $this->person_full_name;
    }

    public function scopeIncoming($query)
    {
        return $query->where('type', 'incoming');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('type', 'outgoing');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'incoming' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'outgoing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'borrow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'return' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            'transfer' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'overdue' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }
}
