<?php

namespace App\Models;

use App\Models\Concerns\HasPublicImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;
use Mews\Purifier\Facades\Purifier;

class ProjectStory extends Model
{
    use HasPublicImage;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'title',
        'content',
        'image_path',
        'code_snippet',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $story): void {
            if (is_string($story->content)) {
                $story->content = Purifier::clean($story->content);
            }
        });
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->publicImageUrl($this->image_path);
    }

    public function getContentHtmlAttribute(): HtmlString
    {
        return new HtmlString($this->content ?? '');
    }
}
