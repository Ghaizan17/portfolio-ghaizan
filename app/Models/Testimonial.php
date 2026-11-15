<?php

namespace App\Models;

use App\Models\Concerns\HasPublicImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory, HasPublicImage;

    public const RELATIONSHIP_TEACHER = 'teacher';

    public const RELATIONSHIP_FRIEND = 'friend';

    public const RELATIONSHIP_USER = 'user';

    public const RELATIONSHIP_SUPERVISOR = 'supervisor';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'position',
        'organization',
        'relationship',
        'content',
        'photo_path',
        'project_id',
        'is_published',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function relationships(): array
    {
        return [
            self::RELATIONSHIP_TEACHER => 'Supervising Teacher',
            self::RELATIONSHIP_FRIEND => 'Friend',
            self::RELATIONSHIP_USER => 'Project User',
            self::RELATIONSHIP_SUPERVISOR => 'Internship Supervisor',
        ];
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)->orderBy('sort_order');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->publicImageUrl($this->photo_path);
    }

    public function getRelationshipLabelAttribute(): string
    {
        return static::relationships()[$this->relationship] ?? ucfirst((string) $this->relationship);
    }
}
