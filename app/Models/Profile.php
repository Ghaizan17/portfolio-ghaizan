<?php

namespace App\Models;

use App\Models\Concerns\HasPublicImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Mews\Purifier\Facades\Purifier;

class Profile extends Model
{
    use HasFactory, HasPublicImage;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'headline',
        'class',
        'vocational_competency',
        'bio_short',
        'bio_long',
        'personal_branding',
        'career_goals',
        'location',
        'availability',
        'avatar_path',
        'email',
        'phone',
    ];

    public static function current(): ?self
    {
        return static::query()->oldest('id')->first();
    }

    protected static function booted(): void
    {
        static::saving(function (self $profile): void {
            foreach (['bio_long', 'career_goals'] as $attribute) {
                if (is_string($profile->{$attribute})) {
                    $profile->{$attribute} = Purifier::clean($profile->{$attribute});
                }
            }
        });
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->publicImageUrl($this->avatar_path);
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', (string) $this->full_name))
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }

    public function getHasAvatarAttribute(): bool
    {
        return filled($this->avatar_path) && Storage::disk('public')->exists($this->avatar_path);
    }
}
