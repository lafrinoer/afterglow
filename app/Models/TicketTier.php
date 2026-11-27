<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['festival_day_id', 'zone_name', 'price', 'quota', 'sold_count', 'benefits'])]
class TicketTier extends Model
{
    protected function casts(): array
    {
        return ['benefits' => 'array'];
    }

    public function festivalDay(): BelongsTo
    {
        return $this->belongsTo(FestivalDay::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
