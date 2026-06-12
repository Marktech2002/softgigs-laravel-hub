<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UploadAvatarRequest;
use App\Services\UserAuthService;
use App\Traits\ApiResponseTraits;
use App\Enums\UserRoles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTraits;

    public function __construct(
        private readonly UserAuthService $authService
    ) {}

    /**
     * Register a new user.
     */
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $data = $this->authService->register(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            phone: $validated['phone'],
            role: UserRoles::from($validated['role'])
        );

        return $this->apiResponse::created('User registered successfully.', $data);
    }

    /**
     * Login user and issue token.
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $data = $this->authService->login(
            email: $validated['email'],
            password: $validated['password']
        );

        return $this->apiResponse::success('Login successful.', $data);
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $data = $this->authService->getProfile($request->user());

        return $this->apiResponse::success('Profile retrieved successfully.', $data);
    }

    /**
     * Logout and revoke tokens.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->apiResponse::success('User logged out successfully.');
    }

    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        $user = $this->authService->uploadAvatar(
            user: $request->user(),
            file: $request->file('avatar')
        );

        return $this->apiResponse::success('Avatar uploaded successfully.', [
            'user' => $user,
            'avatar_url' => asset('storage/' . $user->avatar),
        ]);
    }
}
