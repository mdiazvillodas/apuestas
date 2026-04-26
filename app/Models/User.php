<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\BrevoMailer;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
        protected $fillable = [
            'name',
            'email',
            'password',
            'role',
            'coins',
        ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    public function ownedLeague()
    {
        return $this->hasOne(League::class, 'owner_id');
    }

    public function leagues()
    {
        return $this->belongsToMany(League::class, 'league_members')
            ->withTimestamps();
    }

    public function leagueJoinRequests()
    {
        return $this->hasMany(LeagueJoinRequest::class);
    }
    
    public function sendEmailVerificationNotification()
    {
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $this->getKey(),
            'hash' => sha1($this->getEmailForVerification()),
        ]
    );

    $html = "
        <h2>Verify your NanoBet account</h2>
        <p>Click the link below to verify your email:</p>
        <a href='{$verificationUrl}'>Verify Email</a>
    ";

    BrevoMailer::send(
        $this->email,
        'Verify your NanoBet account',
        $html
    );
    }

}
