<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    public static function get(string $key, $default = null)
    {
        $cacheKey = 'settings.' . $key;
        return Cache::rememberForever($cacheKey, function () use ($key, $default) {
            $row = Setting::where('key', $key)->first();
            return $row ? (is_numeric($row->value) ? $row->value + 0 : $row->value) : $default;
        });
    }

    public static function set(string $key, $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings.' . $key);
    }
}

