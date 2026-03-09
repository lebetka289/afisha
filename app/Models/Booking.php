<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'reference',
        'customer_name',
        'customer_email',
        'customer_phone',
        'tickets_count',
        'total_amount',
        'status',
        'payment_method',
        'booked_at',
        'notes',
        'meta',
    ];

    protected $casts = [
        'booked_at' => 'datetime',
        'meta' => 'array',
        'total_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (blank($booking->reference)) {
                $booking->reference = strtoupper(Str::random(8));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(BookingAddon::class);
    }
}
