<?php

namespace App\Models;

use App\Enums\RoleEnum;
use App\Observers\PostObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

#[ObservedBy([PostObserver::class])]
class Post extends Model
{
    public const NAV_BADGE_CACHE_KEY = 'posts.count';

    public static function clearCountCache(): void
    {
        cache()->forget(self::NAV_BADGE_CACHE_KEY);
    }

    public static function cachedCount(?User $user = null): int
    {
        $user ??= auth()->user();
        $cacheKey = self::NAV_BADGE_CACHE_KEY . '.' . ($user?->getAuthIdentifier() ?? 'guest');

        return cache()->remember(
            $cacheKey,
            now()->addMinutes(5),
            fn () => self::queryForUser($user)->count()
        );
    }

    public static function queryForUser(?User $user = null): Builder
    {
        $user ??= auth()->user();
        return self::query()->forUser($user);
    }

    public function scopeForUser(Builder $query, ?User $user = null): Builder
    {
        if ($user?->hasRole(RoleEnum::Editor->value)) {
            $query->where('user_id', $user->getAuthIdentifier());
        }

        return $query;
    }

    protected $fillable = [
        'title',
        'slug',
        'user_id',
        'category_id',
        'color',
        'image',
        'content',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = static::generateUniqueSlug($post->title);
            }
        });
    }

    public static function generateUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count++;
        }

        return $slug;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
