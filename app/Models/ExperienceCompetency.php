<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $experience_id
 * @property string $name
 * @property-read Experience $experience
 */
class ExperienceCompetency extends Model
{
    protected $fillable = [
        'experience_id',
        'name',
    ];

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }
}
