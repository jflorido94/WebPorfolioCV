<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $role
 * @property string $company
 * @property string|null $location
 * @property string $period
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $ended_at
 * @property bool $show_in_web
 * @property bool $show_in_pdf
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ExperienceCompetency> $competencies
 */
class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'company',
        'location',
        'period',
        'description',
        'started_at',
        'ended_at',
        'show_in_web',
        'show_in_pdf',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
            'show_in_web' => 'boolean',
            'show_in_pdf' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function competencies(): HasMany
    {
        return $this->hasMany(ExperienceCompetency::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('started_at')->orderByDesc('ended_at');
    }
}
