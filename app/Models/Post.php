<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use App\Models\Concerns\HasPublicImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\HtmlString;
use Mews\Purifier\Facades\Purifier;

class Post extends Model
{
    use GeneratesSlug, HasFactory, HasPublicImage, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'post_category_id',
        'user_id',
        'title',
        'slug',
        'thumbnail',
        'excerpt',
        'content',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $post): void {
            if (is_string($post->content)) {
                $post->content = Purifier::clean($post->content);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<PostCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsToMany<PostTag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(PostTag::class, 'post_tag', 'post_id', 'post_tag_id');
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderByDesc('published_at')->orderByDesc('id');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->publicImageUrl($this->thumbnail);
    }

    public function getContentHtmlAttribute(): HtmlString
    {
        return new HtmlString($this->content ?? '');
    }

    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags((string) $this->content));

        return max(1, (int) ceil($words / 220));
    }
}
