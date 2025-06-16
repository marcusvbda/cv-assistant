<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'position',
        'password',
        'introduction',
        'ai_integration'
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
            'phones' => 'array',
            'urls' => 'array',
            'skills' => 'array',
            'ai_integration' => 'object'
        ];
    }

    public function phones()
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }

    public function links()
    {
        return $this->morphMany(Link::class, 'linkable');
    }

    public function skills()
    {
        return $this->morphMany(Skill::class, 'skillable');
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function courses()
    {
        return $this->morphMany(Course::class, 'courseable');
    }

    public function experiences()
    {
        return $this->morphMany(Experience::class, 'experienceable');
    }

    public function projects()
    {
        return $this->morphMany(Project::class, 'projectable');
    }

    public function certificates()
    {
        return $this->morphMany(Certificate::class, 'certificateable');
    }

    public function hasAiIntegration(): bool
    {
        $user = Auth::user();
        $ai_integration = $user->ai_integration ?? [];
        return boolval(data_get($ai_integration, "key"));
    }
}
