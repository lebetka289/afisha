<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'event_section_id',
        'event_seat_id',
        'seat_label',
        'price',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'price' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(EventSection::class, 'event_section_id');
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(EventSeat::class, 'event_seat_id');
    }
}
