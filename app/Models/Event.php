<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Bet;
use App\Models\Team;

class Event extends Model
{
    protected $fillable = [
        'title',
        'team_a_id',
        'team_b_id',
        'betting_opens_at',
        'betting_closes_at',
        'starts_at',
        'status',
        'result',
    ];

    protected $casts = [
        'betting_opens_at'  => 'datetime',
        'betting_closes_at' => 'datetime',
        'starts_at'         => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function teamA()
    {
        return $this->belongsTo(Team::class, 'team_a_id');
    }

    public function teamB()
    {
        return $this->belongsTo(Team::class, 'team_b_id');
    }

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Estado calculado del evento
    |--------------------------------------------------------------------------
    */

    public function getComputedStatusAttribute(): string
    {
        $now = now();

        // Evento ya liquidado
        if ($this->status === 'finished') {
            return 'finished';
        }

        // Evento aún no abierto
        if ($this->status === 'draft') {
            return 'draft';
        }

        // Evento ya comenzó → cerrado para apostar
        if ($this->starts_at && $now->greaterThanOrEqualTo($this->starts_at)) {
            return 'closed';
        }

        // Apuestas cerradas por tiempo
        if ($this->betting_closes_at && $now->greaterThanOrEqualTo($this->betting_closes_at)) {
            return 'closed';
        }

        // Único caso apostable
        return 'open';
    }

    /*
    |--------------------------------------------------------------------------
    | Payout (parimutuel)
    |--------------------------------------------------------------------------
    */

    public function payoutFor(string $selection): float
    {
        $totalPool = $this->bets()->sum('amount');

        if ($totalPool === 0) {
            return 1.0;
        }

        $selectionPool = $this->bets()
            ->where('selection', $selection)
            ->sum('amount');

        if ($selectionPool === 0) {
            return 1.0;
        }

        return round($totalPool / $selectionPool, 2);
    }
}
