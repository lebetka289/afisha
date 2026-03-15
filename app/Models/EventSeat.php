<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventSeat extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_section_id',
        'label',
        'row_number',
        'col_number',
        'status',
        'price',
        'position',
        'meta',
    ];

    protected $casts = [
        'position' => 'array',
        'meta' => 'array',
        'price' => 'decimal:2',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(EventSection::class, 'event_section_id');
    }
}
