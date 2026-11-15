<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
    ];

    public const CACHE_KEY = 'digital-dimension.settings';

    protected static function booted(): void
    {
        static::saved(fn (): mixed => Cache::forget(self::CACHE_KEY));
        static::deleted(fn (): mixed => Cache::forget(self::CACHE_KEY));
    }

    /**
     * @return array<string, string|null>
     */
    public static function allCached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): array => static::query()->pluck('value', 'key')->all());
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::allCached()[$key] ?? $default;
    }

    public static function set(string $key, ?string $value, string $group = 'general', string $type = 'string'): self
    {
        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type],
        );
    }
}
