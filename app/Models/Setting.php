<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        // Cache the setting for performance
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return bool
     */
    public static function set($key, $value, $type = 'string')
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => self::prepareValue($value, $type),
                'type' => $type
            ]
        );

        // Clear the cache for this setting
        Cache::forget("setting.{$key}");

        return $setting ? true : false;
    }

    /**
     * Get all settings as key-value pairs
     *
     * @param string|null $group
     * @return array
     */
    public static function getAllSettings($group = null)
    {
        $query = self::query();

        if ($group) {
            $query->where('group', $group);
        }

        $settings = $query->get();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = self::castValue($setting->value, $setting->type);
        }

        return $result;
    }

    /**
     * Get settings grouped by their group field
     *
     * @return array
     */
    public static function getGroupedSettings()
    {
        $settings = self::all();
        $grouped = [];

        foreach ($settings as $setting) {
            $group = $setting->group ?: 'general';
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][$setting->key] = self::castValue($setting->value, $setting->type);
        }

        return $grouped;
    }

    /**
     * Cast value based on type
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);

            case 'integer':
            case 'int':
                return (int) $value;

            case 'float':
            case 'double':
                return (float) $value;

            case 'json':
            case 'array':
                $decoded = json_decode($value, true);
                return $decoded ?: [];

            case 'string':
            case 'text':
            case 'email':
            case 'url':
            default:
                return (string) $value;
        }
    }

    /**
     * Prepare value for storage based on type
     *
     * @param mixed $value
     * @param string $type
     * @return string
     */
    protected static function prepareValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
            case 'bool':
                return $value ? '1' : '0';

            case 'json':
            case 'array':
                return json_encode($value);

            default:
                return (string) $value;
        }
    }

    /**
     * Clear all settings cache
     *
     * @return void
     */
    public static function clearCache()
    {
        $settings = self::all();

        foreach ($settings as $setting) {
            Cache::forget("setting.{$setting->key}");
        }
    }

    /**
     * Boot the model
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when a setting is updated
        static::saved(function ($setting) {
            Cache::forget("setting.{$setting->key}");
        });

        // Clear cache when a setting is deleted
        static::deleted(function ($setting) {
            Cache::forget("setting.{$setting->key}");
        });
    }

    /**
     * Scope to filter by group
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Check if a setting exists
     *
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return self::where('key', $key)->exists();
    }

    /**
     * Delete a setting by key
     *
     * @param string $key
     * @return bool
     */
    public static function remove($key)
    {
        Cache::forget("setting.{$key}");
        return self::where('key', $key)->delete() > 0;
    }
}
