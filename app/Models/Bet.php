<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'selection',
        'amount',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    // relaciones
    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    // total apostado en el evento
    public function totalPool(): int
    {
        return $this->bets()->sum('amount');
    }

    // total apostado a un resultado específico
    public function poolFor(string $selection): int
    {
        return $this->bets()
            ->where('selection', $selection)
            ->sum('amount');
    }

    // payout estimado para un resultado
    public function payoutFor(string $selection): float
    {
        $total = $this->totalPool();
        $pool  = $this->poolFor($selection);

        // si nadie apostó todavía → payout neutro
        if ($pool === 0 || $total === 0) {
            return 1.0;
        }

        return round($total / $pool, 2);
    }    
}
