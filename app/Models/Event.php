<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'venue_id',
        'artist_id',
        'created_by',
        'title',
        'slug',
        'subtitle',
        'category',
        'description',
        'start_at',
        'end_at',
        'sales_start_at',
        'sales_end_at',
        'status',
        'poster_url',
        'max_tickets',
        'layout_type',
        'layout_config',
        'meta',
        'latitude',
        'longitude',
    ];

    protected $appends = [
        'poster_src',
        'poster_is_video',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'sales_start_at' => 'datetime',
        'sales_end_at' => 'datetime',
        'layout_config' => 'array',
        'meta' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            if (blank($event->slug)) {
                $event->slug = Str::slug($event->title) . '-' . Str::random(4);
            }
        });
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(EventSection::class)->orderBy('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(EventAddon::class, 'event_id')->orderBy('sort_order');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_favorites', 'event_id', 'user_id')->withTimestamps();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getPosterSrcAttribute(): ?string
    {
        if (blank($this->poster_url)) {
            return null;
        }

        if (filter_var($this->poster_url, FILTER_VALIDATE_URL)) {
            return $this->poster_url;
        }

        return route('media.show', ['path' => $this->poster_url]);
    }

    public function getPosterIsVideoAttribute(): bool
    {
        if (blank($this->poster_url)) {
            return false;
        }

        $path = parse_url($this->poster_url, PHP_URL_PATH) ?: $this->poster_url;

        return Str::endsWith(Str::lower($path), ['.mp4', '.webm', '.mov']);
    }
}
