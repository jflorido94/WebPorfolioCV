<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $post_id
 * @property string $tag
 * @property-read Post $post
 */
class PostTag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'tag',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
