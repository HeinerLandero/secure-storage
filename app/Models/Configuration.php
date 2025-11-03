<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a configuration value by key.
     */
    public static function getValue(string $key, string $default = null): ?string
    {
        $config = static::where('key', $key)->first();
        return $config ? $config->value : $default;
    }

    /**
     * Set a configuration value.
     */
    public static function setValue(string $key, string $value): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
