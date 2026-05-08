<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $bio
 * @property string|null $contact_email
 * @property string|null $location
 * @property string|null $github_url
 * @property string|null $linkedin_url
 * @property string|null $avatar_initials
 * @property string|null $avatar_path
 * @property-read string $initials
 * @property-read User $user
 */
class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'bio',
        'contact_email',
        'location',
        'github_url',
        'linkedin_url',
        'avatar_initials',
        'avatar_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getInitialsAttribute(): string
    {
        if (! empty($this->avatar_initials)) {
            return $this->avatar_initials;
        }

        $name = $this->user?->name ?? '';

        return Str::of($name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => Str::upper(Str::substr($part, 0, 1)))
            ->implode('') ?: 'JF';
    }
}
