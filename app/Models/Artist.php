<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'photo',
        'links',
    ];

    protected $casts = [
        'links' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Artist $artist) {
            if (blank($artist->slug)) {
                $artist->slug = Str::slug($artist->name) . '-' . Str::random(4);
            }
        });
    }

    protected $appends = [
        'photo_src',
        'photo_is_video',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function getPhotoSrcAttribute(): ?string
    {
        if (blank($this->photo)) {
            return null;
        }

        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }

        return route('media.show', ['path' => $this->photo]);
    }

    public function getPhotoIsVideoAttribute(): bool
    {
        if (blank($this->photo)) {
            return false;
        }

        $path = parse_url($this->photo, PHP_URL_PATH) ?: $this->photo;

        return Str::endsWith(Str::lower($path), ['.mp4', '.webm', '.mov']);
    }
}
