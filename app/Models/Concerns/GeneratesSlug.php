<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait GeneratesSlug
{
    protected static function bootGeneratesSlug(): void
    {
        static::saving(function (self $model): void {
            $source = $model->getSlugSource();

            if (blank($model->slug) && filled($source)) {
                $model->slug = $model->generateUniqueSlug($source);
            }
        });
    }

    protected function getSlugSource(): ?string
    {
        return $this->title ?? $this->name ?? null;
    }

    protected function generateUniqueSlug(string $source): string
    {
        $base = Str::slug($source) ?: Str::random(6);
        $slug = $base;
        $counter = 2;

        while (static::query()->where('slug', $slug)->when($this->exists, fn ($query) => $query->whereKeyNot($this->getKey()))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
