<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Models\Listing;
use App\Services\ListingsService;
use App\Traits\ApiResponseTraits;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    use ApiResponseTraits;

    public function __construct(
        private readonly ListingsService $listingsService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', $request->query('per_page', 15));
        $page = (int) $request->query('page', 1);
        $search = $request->query('search');

        $paginator = $this->listingsService->getAllListings($search, $limit, $page);

        return $this->apiResponse::success('Listings retrieved successfully.', [
            'data' => $paginator->items(),
            'pagination' => [
                'total_documents' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreListingRequest $request): JsonResponse
    {
        $listing = $this->listingsService->createListing(
            data: $request->validated(),
            user: $request->user()
        );

        return $this->apiResponse::created('Listing created successfully.', $listing);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $listing = $this->listingsService->getListingById($id);

        if (! $listing) {
            return $this->apiResponse::notFound('Listing not found.');
        }

        return $this->apiResponse::success('Listing retrieved successfully.', $listing);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateListingRequest $request, Listing $listing): JsonResponse
    {
        $updatedListing = $this->listingsService->updateListing(
            listing: $listing,
            data: $request->validated(),
            user: $request->user()
        );

        return $this->apiResponse::success('Listing updated successfully.', $updatedListing);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Listing $listing): JsonResponse
    {
        $this->listingsService->deleteListing(
            listing: $listing,
            user: $request->user()
        );

        return $this->apiResponse::success('Listing deleted successfully.');
    }
}
