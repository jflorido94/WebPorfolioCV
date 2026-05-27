<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $level
 * @property bool $show_in_web
 * @property bool $show_in_pdf
 */
class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'level',
        'show_in_web',
        'show_in_pdf',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'show_in_web' => 'boolean',
            'show_in_pdf' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
