<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_encrypted'
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    /**
     * Get setting value with caching
     */
    public static function get($key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }

            // Handle empty or null values for encrypted fields
            if ($setting->is_encrypted && (empty($setting->value) || is_null($setting->value))) {
                return $default;
            }

            try {
                $value = $setting->is_encrypted ? Crypt::decrypt($setting->value) : $setting->value;
            } catch (\Exception $e) {
                // If decryption fails, return default value
                return $default;
            }

            // Convert value based on type
            switch ($setting->type) {
                case 'boolean':
                    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                case 'integer':
                    return (int) $value;
                case 'json':
                    return json_decode($value, true);
                default:
                    return $value;
            }
        });
    }

    /**
     * Set setting value with caching
     */
    public static function set($key, $value, $type = 'string', $group = 'general', $label = null, $description = null, $isEncrypted = false)
    {
        // Convert value to string for storage
        $storedValue = $value;
        if ($type === 'json') {
            $storedValue = json_encode($value);
        } elseif ($type === 'boolean') {
            $storedValue = $value ? '1' : '0';
        } else {
            $storedValue = (string) $value;
        }

        // Only encrypt if value is not empty and encryption is requested
        if ($isEncrypted && !empty($storedValue)) {
            $storedValue = Crypt::encrypt($storedValue);
        } elseif ($isEncrypted && empty($storedValue)) {
            // Don't encrypt empty values
            $isEncrypted = false;
        }

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $storedValue,
                'type' => $type,
                'group' => $group,
                'label' => $label,
                'description' => $description,
                'is_encrypted' => $isEncrypted
            ]
        );

        // Clear cache
        Cache::forget("setting.{$key}");

        return $setting;
    }

    /**
     * Remove setting
     */
    public static function remove($key)
    {
        Cache::forget("setting.{$key}");
        return static::where('key', $key)->delete();
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup($group)
    {
        return static::where('group', $group)->get()->mapWithKeys(function ($setting) use ($group) {
            // Handle empty or null values for encrypted fields
            if ($setting->is_encrypted && (empty($setting->value) || is_null($setting->value))) {
                $key = str_replace($group . '.', '', $setting->key);
                return [$key => null];
            }

            try {
                $value = $setting->is_encrypted ? Crypt::decrypt($setting->value) : $setting->value;
            } catch (\Exception $e) {
                // If decryption fails, return null
                $key = str_replace($group . '.', '', $setting->key);
                return [$key => null];
            }
            
            // Convert value based on type
            switch ($setting->type) {
                case 'boolean':
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'integer':
                    $value = (int) $value;
                    break;
                case 'json':
                    $value = json_decode($value, true);
                    break;
            }

            // Remove group prefix from key for easier access in views
            $key = str_replace($group . '.', '', $setting->key);
            return [$key => $value];
        });
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache()
    {
        $settings = static::all();
        foreach ($settings as $setting) {
            Cache::forget("setting.{$setting->key}");
        }
    }
}
