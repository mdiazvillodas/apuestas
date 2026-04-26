<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeagueJoinRequest extends Model
{
    protected $fillable = [
        'league_id',
        'user_id',
        'status',
    ];

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
