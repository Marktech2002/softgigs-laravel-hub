<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ListingsService
{
    /**
     * Retrieve all listings with caching, optional search, and pagination.
     */
    public function getAllListings(?string $search = null, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $version = $this->getCacheVersion();
        
        $cacheKey = "listings.v{$version}.index.search." . ($search ?? 'none') . ".perPage.{$perPage}.page.{$page}";

        return Cache::remember($cacheKey, 3600, function () use ($search, $perPage, $page) {
            $query = Listing::with('user')->latest();

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('tags', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%")
                      ->orWhere('date', 'like', "%{$search}%");
                });
            }

            return $query->paginate(perPage: $perPage, page: $page);
        });
    }

    /**
     * Retrieve a specific listing by ID with caching.
     */
    public function getListingById(int $id): ?Listing
    {
        return Cache::remember("listings.show.{$id}", 3600, function () use ($id) {
            return Listing::with('user')->find($id);
        });
    }

    /**
     * Create a new listing.
     */
    public function createListing(array $data, User $user): Listing
    {
        $data['created_by'] = $user->id;

        $listing = Listing::create($data);

        $this->invalidateCache();

        return $listing;
    }

    /**
     * Update an existing listing.
     */
    public function updateListing(Listing $listing, array $data, User $user): Listing
    {
        if ($listing->created_by !== $user->id) {
            throw new AccessDeniedHttpException('You do not have permission to modify this listing.');
        }

        try {
            $listing->update($data);

            $this->invalidateCache($listing->id);

            return $listing;
        } catch (\Throwable $th) {
            Log::error('Error updating listing', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'user_id' => $user->id,
                'listing_id' => $listing->id,
                'data' => $data,
            ]);

            throw $th;
        }
    }

    /**
     * Delete an existing listing.
     */
    public function deleteListing(Listing $listing, User $user): bool
    {
        if ($listing->created_by !== $user->id) {
            throw new AccessDeniedHttpException('You do not have permission to delete this listing.');
        }

        try {
            return DB::transaction(function () use ($listing) {
                // Placed inside a transaction for future related deletions
                $deleted = $listing->delete();

                $this->invalidateCache($listing->id);

                return $deleted;
            });
        } catch (\Throwable $th) {
            Log::error('Error deleting listing', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'user_id' => $user->id,
                'listing_id' => $listing->id,
            ]);

            throw $th;
        }
    }

    /**
     * Get the current cache version for listings.
     */
    private function getCacheVersion(): int
    {
        return Cache::rememberForever('listings_cache_version', fn () => 1);
    }

    /**
     * Invalidate listings cache keys by incrementing version.
     */
    private function invalidateCache(?int $listingId = null): void
    {
        Cache::increment('listings_cache_version');

        if ($listingId) {
            Cache::forget("listings.show.{$listingId}");
        }
    }
}
