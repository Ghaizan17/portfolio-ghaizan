<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use App\Models\Concerns\HasPublicImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Mews\Purifier\Facades\Purifier;

class Project extends Model
{
    use GeneratesSlug, HasFactory, HasPublicImage, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_category_id',
        'title',
        'slug',
        'excerpt',
        'description',
        'problem_statement',
        'objectives',
        'role',
        'team_collaboration',
        'year',
        'featured',
        'cover_path',
        'demo_url',
        'github_url',
        'is_published',
        'published_at',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'year' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saving(function (self $project): void {
            foreach (['description', 'problem_statement', 'objectives', 'team_collaboration'] as $attribute) {
                if (is_string($project->{$attribute})) {
                    $project->{$attribute} = Purifier::clean($project->{$attribute});
                }
            }
        });
    }

    /**
     * @return BelongsTo<ProjectCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    /**
     * @return BelongsToMany<Technology, $this>
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ProjectMedia, $this>
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProjectMedia::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ProjectFeature, $this>
     */
    public function features(): HasMany
    {
        return $this->hasMany(ProjectFeature::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ProjectProcess, $this>
     */
    public function processes(): HasMany
    {
        return $this->hasMany(ProjectProcess::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ProjectFeedback, $this>
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(ProjectFeedback::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ProjectStory, $this>
     */
    public function stories(): HasMany
    {
        return $this->hasMany(ProjectStory::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<Testimonial, $this>
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
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
        $query->orderByDesc('featured')->orderBy('sort_order')->orderByDesc('year');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->publicImageUrl($this->cover_path);
    }

    public function getScreenshotsAttribute(): Collection
    {
        return $this->media->where('type', 'screenshot');
    }

    public function getDocumentationMediaAttribute(): Collection
    {
        return $this->media->where('type', 'documentation');
    }

    public function getPublishedFeedbackAttribute(): Collection
    {
        return $this->feedback->where('is_published', true);
    }

    public function getDemoIsValidAttribute(): bool
    {
        return filter_var($this->demo_url, FILTER_VALIDATE_URL) !== false;
    }

    public function getGithubIsValidAttribute(): bool
    {
        return filter_var($this->github_url, FILTER_VALIDATE_URL) !== false;
    }
}
