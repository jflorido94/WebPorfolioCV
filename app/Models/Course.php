<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $title
 * @property string $institution
 * @property string|null $location
 * @property int|null $year
 * @property string|null $url
 * @property bool $show_in_web
 * @property bool $show_in_pdf
 * @property-read User $user
 */
class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'institution',
        'location',
        'year',
        'url',
        'show_in_web',
        'show_in_pdf',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'show_in_web' => 'boolean',
            'show_in_pdf' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('year');
    }

    public function scopeCourses(Builder $query): Builder
    {
        return $query->where('type', 'course');
    }

    public function scopeCertifications(Builder $query): Builder
    {
        return $query->where('type', 'certification');
    }
}
