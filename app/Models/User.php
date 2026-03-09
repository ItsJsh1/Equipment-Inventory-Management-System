<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'middlename',
        'email',
        'password',
        'department_id',
        'employee_id',
        'contact_number',
        'profile_picture',
        'avatar',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['firstname', 'lastname', 'email', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get full name attribute.
     */
    public function getNameAttribute(): string
    {
        return trim("{$this->firstname} {$this->middlename} {$this->lastname}");
    }

    /**
     * Get full name attribute (alternative).
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get profile picture URL.
     */
    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        
        // Return default avatar with initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Department relationship.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Transactions processed by this user.
     */
    public function processedTransactions()
    {
        return $this->hasMany(Transaction::class, 'processed_by');
    }

    /**
     * Borrowings approved by this user.
     */
    public function processedBorrowings()
    {
        return $this->hasMany(Borrowing::class, 'approved_by');
    }

    /**
     * Maintenances created by this user.
     */
    public function scheduledMaintenances()
    {
        return $this->hasMany(Maintenance::class, 'created_by');
    }

    /**
     * Disposals requested by this user.
     */
    public function requestedDisposals()
    {
        return $this->hasMany(Disposal::class, 'requested_by');
    }
}
