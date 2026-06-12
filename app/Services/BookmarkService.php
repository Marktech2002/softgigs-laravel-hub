<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Listing;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class BookmarkService
{
    /**
     * Toggle a bookmark for a specific listing and user.
     * Returns true if bookmarked, false if unbookmarked.
     */
    public function toggleBookmark(User $user, Listing $listing): bool
    {
        $toggled = $user->bookmarkedListings()->toggle($listing->id);
        
        // Invalidate user bookmarks cache using a version key
        Cache::forever("bookmarks_cache_version_user_{$user->id}", microtime(true));

        return count($toggled['attached']) > 0;
    }

    /**
     * Get paginated bookmarked listings for a user.
     */
    public function getUserBookmarks(User $user, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $version = Cache::get("bookmarks_cache_version_user_{$user->id}", 1);
        
        $cacheKey = "bookmarks.v{$version}.user_{$user->id}.perPage_{$perPage}.page_{$page}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($user, $perPage, $page) {
            return $user->bookmarkedListings()
                ->orderByPivot('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        });
    }
}
