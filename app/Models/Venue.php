<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'city',
        'address',
        'latitude',
        'longitude',
        'description',
        'max_capacity',
        'layout_type',
        'layout_config',
    ];

    protected $casts = [
        'layout_config' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
