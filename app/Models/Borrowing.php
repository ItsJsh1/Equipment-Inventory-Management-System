<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Borrowing extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'borrowing_code',
        'equipment_id',
        'transaction_id',
        'borrower_firstname',
        'borrower_lastname',
        'borrower_middlename',
        'department_id',
        'contact_number',
        'email',
        'id_number',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'purpose',
        'remarks',
        'status',
        'condition_on_borrow',
        'condition_on_return',
        'approved_by',
        'received_by',
        'created_by',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'borrowing_code', 'equipment_id', 'borrower_firstname',
                'borrower_lastname', 'borrow_date', 'expected_return_date', 'status'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($borrowing) {
            if (empty($borrowing->borrowing_code)) {
                $prefix = Setting::getValue('borrowing_code_prefix', 'BRW');
                $lastBorrowing = self::withTrashed()->orderBy('id', 'desc')->first();
                $nextId = $lastBorrowing ? $lastBorrowing->id + 1 : 1;
                $borrowing->borrowing_code = $prefix . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }

            if (auth()->check()) {
                $borrowing->created_by = auth()->id();
            }
        });
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getBorrowerFullNameAttribute(): string
    {
        return trim("{$this->borrower_firstname} {$this->borrower_middlename} {$this->borrower_lastname}");
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', 'borrowed');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('status', 'borrowed')
                    ->where('expected_return_date', '<', now());
            });
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'borrowed' && $this->expected_return_date < now();
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'borrowed' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'returned' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'lost' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
            'damaged' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }
}
