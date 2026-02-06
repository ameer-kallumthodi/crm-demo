<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        // Cast value based on type
        switch ($setting->type) {
            case 'boolean':
                return (bool) $setting->value;
            case 'number':
                return is_numeric($setting->value) ? (float) $setting->value : $default;
            case 'json':
                return json_decode($setting->value, true) ?? $default;
            default:
                return $setting->value ?? $default;
        }
    }

    /**
     * Set setting value by key
     */
    public static function set($key, $value, $type = 'text', $description = null, $group = 'general')
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            $setting = new static();
            $setting->key = $key;
            $setting->type = $type;
            $setting->description = $description;
            $setting->group = $group;
        }

        // Convert value based on type
        switch ($type) {
            case 'boolean':
                $setting->value = $value ? '1' : '0';
                break;
            case 'json':
                $setting->value = is_array($value) ? json_encode($value) : $value;
                break;
            default:
                $setting->value = (string) $value;
        }

        $setting->save();
        return $setting;
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup($group)
    {
        return static::where('group', $group)->get();
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllAsArray()
    {
        return static::pluck('value', 'key')->toArray();
    }
}