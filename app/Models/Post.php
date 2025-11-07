<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'status',
        'source',
        'external_id',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
        ];
    }

    /**
     * Get status as enum (helper method)
     */
    public function getStatusEnum(): PostStatus
    {
        return PostStatus::from($this->attributes['status'] ?? PostStatus::DRAFT->value);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope a query to only include published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', PostStatus::PUBLISHED->value);
    }

    /**
     * Check if a post with the given source and external_id already exists
     *
     * @deprecated Use PostRepository::findBySourceAndExternalId instead
     */
    public static function isDuplicate(string $source, string $externalId): bool
    {
        return static::where('source', $source)
            ->where('external_id', $externalId)
            ->exists();
    }
}
