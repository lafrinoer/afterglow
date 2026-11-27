<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['day_number', 'title', 'event_date', 'description'])]
class FestivalDay extends Model
{
    protected function casts(): array
    {
        return ['event_date' => 'date'];
    }

    public function ticketTiers(): HasMany
    {
        return $this->hasMany(TicketTier::class);
    }
}
