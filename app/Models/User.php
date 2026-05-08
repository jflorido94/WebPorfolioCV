<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property bool $is_admin
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Profile|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Experience> $experiences
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Education> $education
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Skill> $skills
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Course> $courses
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Post> $posts
 * @property-read int $years_of_experience
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Relations eager-loaded for the public CV view across the app.
     *
     * @var list<string>
     */
    public const CV_RELATIONS = ['profile', 'experiences.competencies', 'education', 'skills', 'courses'];

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class)->ordered();
    }

    public function education(): HasMany
    {
        return $this->hasMany(Education::class)->ordered();
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class)->ordered();
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class)->ordered();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function scopeWithCv(Builder $query): Builder
    {
        return $query->with(self::CV_RELATIONS);
    }

    public function getYearsOfExperienceAttribute(): int
    {
        $start = $this->experiences
            ->pluck('started_at')
            ->filter()
            ->min();

        if ($start === null) {
            return 0;
        }

        return max(0, now()->year - $start->year);
    }
}
