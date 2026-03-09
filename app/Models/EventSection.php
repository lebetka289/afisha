<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'type',
        'seating_mode',
        'capacity',
        'price',
        'rows',
        'cols',
        'seat_map',
        'position',
        'color',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'seat_map' => 'array',
        'position' => 'array',
        'meta' => 'array',
        'price' => 'decimal:2',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(EventSeat::class);
    }

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }
}
