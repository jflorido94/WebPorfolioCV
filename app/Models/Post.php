<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string|null $summary
 * @property string $content
 * @property string $category
 * @property bool $published
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PostTag> $tags
 * @property-read array<int, string> $tag_list
 */
class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'summary',
        'content',
        'category',
        'published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $post): void {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(PostTag::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc');
    }

    public function scopeLatestPublished(Builder $query, int $limit): Builder
    {
        return $query->published()->limit($limit);
    }

    public function getTagListAttribute(): array
    {
        return $this->tags->pluck('tag')->toArray();
    }
}
