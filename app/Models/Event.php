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
        'team_a_score',
        'team_b_score',
        'match_status_short',
        'match_status_long',
        'score_updated_at',
        'external_id',
        'source',
        'round',
        'auto_managed',
    ];

    protected $casts = [
        'betting_opens_at'  => 'datetime',
        'betting_closes_at' => 'datetime',
        'starts_at'         => 'datetime',
        'score_updated_at'   => 'datetime',
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
        $now = now('UTC');

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

    public function getHasScoreAttribute(): bool
    {
        return ! is_null($this->team_a_score) && ! is_null($this->team_b_score);
    }

    public function getIsLiveAttribute(): bool
    {
        return in_array($this->match_status_short, ['1H', 'HT', '2H', 'ET', 'BT', 'P', 'SUSP', 'INT', 'LIVE'], true);
    }

    public function getHasFinalScoreAttribute(): bool
    {
        return in_array($this->match_status_short, ['FT', 'AET', 'PEN'], true);
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
    /*public function payoutFor(string $selection): float
    {
        // Liquidez base simulada por lado
        $baseLiquidity = 100;

        // Pool real total
        $realTotalPool = $this->bets()->sum('amount');

        // Pool real del lado seleccionado
        $realSelectionPool = $this->bets()
            ->where('selection', $selection)
            ->sum('amount');

        // Ajuste con liquidez simulada
        $adjustedTotalPool = $realTotalPool + ($baseLiquidity * 2);
        $adjustedSelectionPool = $realSelectionPool + $baseLiquidity;

        return round($adjustedTotalPool / $adjustedSelectionPool, 2);
    }     */   
}
