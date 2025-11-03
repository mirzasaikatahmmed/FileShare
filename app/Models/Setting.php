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
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get setting value with proper type casting.
     */
    public function getTypedValue()
    {
        return match ($this->type) {
            'integer' => (int) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Get setting by key.
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return $setting->getTypedValue();
    }

    /**
     * Set setting value.
     */
    public static function set($key, $value, $type = 'string', $description = null)
    {
        if ($type === 'json') {
            $value = json_encode($value);
        } elseif ($type === 'boolean') {
            $value = $value ? '1' : '0';
        }

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
            ]
        );
    }
}
