<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'educations';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'institution',
        'degree',
        'field_of_study',
        'level',
        'start_year',
        'end_year',
        'is_current',
        'description',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_year' => 'integer',
            'end_year' => 'integer',
            'is_current' => 'boolean',
        ];
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderByDesc('start_year');
    }

    public function getPeriodAttribute(): string
    {
        $start = (string) $this->start_year;
        $end = $this->is_current ? 'Present' : (string) ($this->end_year ?? '—');

        return "{$start} — {$end}";
    }
}
