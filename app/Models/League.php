<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $fillable = [
        'name',
        'owner_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'league_members')
            ->withTimestamps();
    }

    public function joinRequests()
    {
        return $this->hasMany(LeagueJoinRequest::class);
    }

    public function pendingJoinRequests()
    {
        return $this->joinRequests()
            ->where('status', 'pending');
    }
}
