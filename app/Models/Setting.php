<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Key/value settings. In this app it only holds the login IP whitelist
 * (`inchecken_ip_whitelist`): IPs that don't trigger the "login page visited" mail.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value): Setting
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getIpWhitelist(): array
    {
        return array_values(array_filter(array_map(
            'trim',
            explode("\n", (string) static::get('inchecken_ip_whitelist', ''))
        )));
    }
}
