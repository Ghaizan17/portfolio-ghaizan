<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Experience extends Model
{
    use HasFactory;

    public const CATEGORY_INTERNSHIP = 'internship';

    public const CATEGORY_ORGANIZATION = 'organization';

    public const CATEGORY_COMMITTEE = 'committee';

    public const CATEGORY_COMPETITION = 'competition';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'organization',
        'position',
        'category',
        'start_date',
        'end_date',
        'is_current',
        'description',
        'responsibilities',
        'achievements',
        'sort_order',
        'is_published',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function categories(): array
    {
        return [
            self::CATEGORY_INTERNSHIP => 'PLK / Internship',
            self::CATEGORY_ORGANIZATION => 'Organization',
            self::CATEGORY_COMMITTEE => 'Committee',
            self::CATEGORY_COMPETITION => 'Competition',
        ];
    }

    /**
     * @return HasMany<ExperienceMedia, $this>
     */
    public function media(): HasMany
    {
        return $this->hasMany(ExperienceMedia::class)->orderBy('sort_order');
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)->orderBy('sort_order')->orderByDesc('start_date');
    }

    public function getCategoryLabelAttribute(): string
    {
        return static::categories()[$this->category] ?? ucfirst((string) $this->category);
    }

    public function getPeriodAttribute(): string
    {
        $start = $this->start_date?->format('M Y') ?? '—';
        $end = $this->is_current ? 'Present' : ($this->end_date?->format('M Y') ?? '—');

        return "{$start} — {$end}";
    }
}
