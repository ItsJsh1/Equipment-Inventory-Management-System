<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Equipment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'equipments';

    protected $fillable = [
        'equipment_code',
        'brand_id',
        'category_id',
        'location_id',
        'model_name',
        'serial_number',
        'specifications',
        'acquisition_date',
        'acquisition_cost',
        'warranty_expiry',
        'status',
        'condition',
        'remarks',
        'image_path',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'warranty_expiry' => 'date',
        'acquisition_cost' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'equipment_code', 'brand_id', 'category_id', 'location_id',
                'model_name', 'serial_number', 'status', 'condition', 'remarks'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Boot method to generate equipment code.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($equipment) {
            if (empty($equipment->equipment_code)) {
                $prefix = Setting::getValue('equipment_code_prefix', 'EQP');
                $lastEquipment = self::withTrashed()->orderBy('id', 'desc')->first();
                $nextId = $lastEquipment ? $lastEquipment->id + 1 : 1;
                $equipment->equipment_code = $prefix . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }

            if (auth()->check()) {
                $equipment->created_by = auth()->id();
            }
        });

        static::updating(function ($equipment) {
            if (auth()->check()) {
                $equipment->updated_by = auth()->id();
            }
        });
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function disposals()
    {
        return $this->hasMany(Disposal::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', 'borrowed');
    }

    public function scopeInMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function scopeForDisposal($query)
    {
        return $query->where('status', 'for_disposal');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'available' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'in_use' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'borrowed' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'maintenance' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'for_disposal' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'disposed' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }

    public function getConditionBadgeAttribute(): string
    {
        return match ($this->condition) {
            'new' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'good' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'fair' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'poor' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'damaged' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }
}
