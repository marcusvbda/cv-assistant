<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Storage;
use marcusvbda\GroqApiService\Settings\GroqApiServiceSettings;
use TomatoPHP\FilamentLanguageSwitcher\Traits\InteractsWithLanguages;

class User extends Authenticatable implements MustVerifyEmail, HasAvatar
{
    use HasFactory, Notifiable, InteractsWithLanguages;

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
        'linkedin',
        'avatar_url',
        'role',
        'custom_fields'
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
            'custom_fields' => 'array'
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
        $settings = app()->make(GroqApiServiceSettings::class)->settings;
        return !empty(data_get($settings, 'key'));
    }

    public static function mapRelationToArray($relation, $fieds, $callback = null): array
    {
        return $relation->get()->map(function ($item) use ($fieds, $callback) {
            $values = collect($item)->only($fieds)->toArray();
            return is_callable($callback) ? $callback($values) : $values;
        })->toArray();
    }

    public function getPayloadContext(): array
    {
        $userArray = $this->toArray();

        unset($userArray['id']);
        unset($userArray['created_at']);
        unset($userArray['updated_at']);
        unset($userArray['ai_integration']);
        unset($userArray['email_verified_at']);

        $userArray = array_merge($userArray, [
            'phones' => static::mapRelationToArray($this->phones(), ['type', 'number']),
            'addresses' => static::mapRelationToArray($this->addresses(), ['city', 'location']),
            'links' =>  static::mapRelationToArray($this->links(), ['name', 'value']),
            'skills' => static::mapRelationToArray($this->skills(), ['type', 'value']),
            'courses' => static::mapRelationToArray($this->courses(), ['instituition', 'start_date', 'end_date', 'name'], function ($row) {
                $row["start_date"] = @$row["start_date"] ? Carbon::parse($row["start_date"])->format('Y-m-d') : null;
                $row["end_date"] = @$row["end_date"] ? Carbon::parse($row["end_date"])->format('Y-m-d')  : null;
                return $row;
            }),
            'experiences' =>  static::mapRelationToArray($this->experiences(), ['position', 'company', 'description', 'start_date', 'end_date'], function ($row) {
                $row["start_date"] = @$row["start_date"] ? Carbon::parse($row["start_date"])->format('Y-m-d') : null;
                $row["end_date"] = @$row["end_date"] ? Carbon::parse($row["end_date"])->format('Y-m-d')  : null;
                return $row;
            }),
            'projects' => static::mapRelationToArray($this->projects(), ['name', 'description', 'start_date', 'end_date'], function ($row) {
                $row["start_date"] = @$row["start_date"] ? Carbon::parse($row["start_date"])->format('Y-m-d') : null;
                $row["end_date"] = @$row["end_date"] ? Carbon::parse($row["end_date"])->format('Y-m-d')  : null;
                return $row;
            }),
            'certificates' => static::mapRelationToArray($this->certificates(), ['name', 'description', 'date'], function ($row) {
                $row["date"] = @$row["date"] ? Carbon::parse($row["date"])->format('Y-m-d') : null;
                return $row;
            })
        ]);

        return $userArray;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }
}
