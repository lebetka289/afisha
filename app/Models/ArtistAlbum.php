<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtistAlbum extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_id',
        'title',
        'year',
        'type',
        'cover_url',
        'link',
        'sort_order',
    ];

    public const TYPE_ALBUM = 'album';
    public const TYPE_SINGLE = 'single';
    public const TYPE_EP = 'ep';

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    protected $appends = ['cover_src'];

    public function getCoverSrcAttribute(): ?string
    {
        if (blank($this->cover_url)) {
            return null;
        }
        if (filter_var($this->cover_url, FILTER_VALIDATE_URL)) {
            return $this->cover_url;
        }
        return route('media.show', ['path' => $this->cover_url]);
    }
}
