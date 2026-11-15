<?php

namespace App\Models;

use App\Models\Concerns\HasPublicImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperienceMedia extends Model
{
    use HasPublicImage;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'experience_id',
        'path',
        'caption',
        'sort_order',
    ];

    /**
     * @return BelongsTo<Experience, $this>
     */
    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }

    public function getUrlAttribute(): ?string
    {
        return $this->publicImageUrl($this->path);
    }
}
