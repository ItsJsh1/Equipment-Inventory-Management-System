<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember("setting.{$key}", 3600, function () use ($key) {
            return self::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set a setting value.
     */
    public static function setValue(string $key, mixed $value): void
    {
        $setting = self::where('key', $key)->first();

        if ($setting) {
            if ($setting->type === 'json' && is_array($value)) {
                $value = json_encode($value);
            }

            $setting->update(['value' => $value]);
        }

        Cache::forget("setting.{$key}");
    }

    /**
     * Get all settings by group.
     */
    public static function getByGroup(string $group): array
    {
        return self::where('group', $group)->pluck('value', 'key')->toArray();
    }
}
