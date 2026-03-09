<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Maintenance extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'maintenance_code',
        'equipment_id',
        'type',
        'title',
        'description',
        'issues_found',
        'actions_taken',
        'parts_replaced',
        'scheduled_date',
        'start_date',
        'completion_date',
        'next_maintenance_date',
        'cost',
        'technician_name',
        'vendor_name',
        'status',
        'equipment_condition_before',
        'equipment_condition_after',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'start_date' => 'date',
        'completion_date' => 'date',
        'next_maintenance_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'maintenance_code', 'equipment_id', 'type', 'title',
                'scheduled_date', 'completion_date', 'status'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($maintenance) {
            if (empty($maintenance->maintenance_code)) {
                $prefix = Setting::getValue('maintenance_code_prefix', 'MNT');
                $lastMaintenance = self::withTrashed()->orderBy('id', 'desc')->first();
                $nextId = $lastMaintenance ? $lastMaintenance->id + 1 : 1;
                $maintenance->maintenance_code = $prefix . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }

            if (auth()->check()) {
                $maintenance->created_by = auth()->id();
            }
        });

        static::updating(function ($maintenance) {
            if (auth()->check()) {
                $maintenance->updated_by = auth()->id();
            }
        });
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays(7));
    }

    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'preventive' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'corrective' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'emergency' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'inspection' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }
}
