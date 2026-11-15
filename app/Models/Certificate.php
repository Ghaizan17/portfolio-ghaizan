<?php

namespace App\Models;

use App\Models\Concerns\HasPublicImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificate extends Model
{
    use HasFactory, HasPublicImage;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'issuer',
        'issued_at',
        'category',
        'description',
        'file_path',
        'file_type',
        'sort_order',
        'is_published',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return HasMany<CertificateMedia, $this>
     */
    public function media(): HasMany
    {
        return $this->hasMany(CertificateMedia::class)->orderBy('sort_order');
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)->orderBy('sort_order')->orderByDesc('issued_at');
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->publicImageUrl($this->file_path);
    }

    public function getIsPdfAttribute(): bool
    {
        return $this->file_type === 'pdf' || str_ends_with((string) $this->file_path, '.pdf');
    }

    /**
     * The best available visual preview: the first supporting image, then the
     * main certificate file when it is an image rather than a PDF.
     */
    public function getPreviewUrlAttribute(): ?string
    {
        $mediaPreview = $this->media->first(
            fn (CertificateMedia $media): bool => filled($media->url),
        );

        if ($mediaPreview) {
            return $mediaPreview->url;
        }

        return $this->is_pdf ? null : $this->file_url;
    }

    /**
     * The disk-relative path Filament should use for admin previews.
     */
    public function getPreviewPathAttribute(): ?string
    {
        $mediaPreview = $this->media->first(
            fn (CertificateMedia $media): bool => filled($media->path),
        );

        if ($mediaPreview) {
            return $mediaPreview->path;
        }

        return $this->is_pdf ? null : $this->file_path;
    }

    protected static function booted(): void
    {
        static::saving(function (self $certificate): void {
            if (blank($certificate->file_path)) {
                return;
            }

            $extension = strtolower(pathinfo($certificate->file_path, PATHINFO_EXTENSION));

            if ($extension === 'pdf') {
                $certificate->file_type = 'pdf';

                return;
            }

            if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'avif'], true)) {
                $certificate->file_type = 'image';
            }
        });
    }
}
