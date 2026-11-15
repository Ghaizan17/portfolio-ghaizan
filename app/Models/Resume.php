<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'label',
        'file_path',
        'is_active',
        'uploaded_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'uploaded_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderByDesc('uploaded_at');
    }

    public static function active(): ?self
    {
        return static::query()->active()->first();
    }

    protected static function booted(): void
    {
        static::saving(function (self $resume): void {
            if ($resume->is_active) {
                static::query()->whereKeyNot($resume->getKey() ?? 0)->update(['is_active' => false]);
            }
        });
    }

    public function getDownloadFilenameAttribute(): string
    {
        return str($this->label)->slug().'.pdf';
    }
}
