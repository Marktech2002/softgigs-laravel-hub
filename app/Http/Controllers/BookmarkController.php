<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Services\BookmarkService;
use App\Traits\ApiResponseTraits;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    use ApiResponseTraits;

    public function __construct(
        private readonly BookmarkService $bookmarkService
    ) {}

    /**
     * Get all bookmarked listings for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('limit', $request->query('per_page', 15));
        $page = (int) $request->query('page', 1);

        $bookmarks = $this->bookmarkService->getUserBookmarks($request->user(), $perPage, $page);

        return $this->apiResponse::success('Bookmarks retrieved successfully.', [
            'data' => $bookmarks->items(),
            'pagination' => [
                'total_documents' => $bookmarks->total(),
                'per_page' => $bookmarks->perPage(),
                'current_page' => $bookmarks->currentPage(),
                'last_page' => $bookmarks->lastPage(),
            ]
        ]);
    }

    /**
     * Toggle a bookmark for a specific listing.
     */
    public function toggle(Request $request, Listing $listing): JsonResponse
    {
        $isBookmarked = $this->bookmarkService->toggleBookmark($request->user(), $listing);

        $message = $isBookmarked ? 'Listing bookmarked successfully.' : 'Listing removed from bookmarks.';

        return $this->apiResponse::success($message, [
            'is_bookmarked' => $isBookmarked,
            'listing_id' => $listing->id,
        ]);
    }
}
