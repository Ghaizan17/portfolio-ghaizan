<?php

namespace App\Models;

use App\Models\Concerns\HasPublicImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateMedia extends Model
{
    use HasPublicImage;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'certificate_id',
        'path',
        'caption',
        'sort_order',
    ];

    /**
     * @return BelongsTo<Certificate, $this>
     */
    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }

    public function getUrlAttribute(): ?string
    {
        return $this->publicImageUrl($this->path);
    }
}
