<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRoles;
use App\Exceptions\AuthenticationException;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class UserAuthService
{
    /**
     * Register a new user account.
     */
    public function register(
        string $name,
        string $email,
        string $password,
        string $phone,
        UserRoles $role
    ): array {
        $normalizedEmail = strtolower(trim($email));

        if (User::where('email', $normalizedEmail)->exists()) {
            throw new AuthenticationException('An account with this email address already exists. Please log in.');
        }

        $user = User::create([
            'name' => $name,
            'email' => $normalizedEmail,
            'password' => Hash::make($password),
            'phone' => $phone,
            'role' => $role->value,
        ]);

        return $this->getProfile($user);
    }

    /**
     * Authenticate a user and issue a Sanctum token.
     */
    public function login(string $email, string $password, int $expiresInHrs = 24): array
    {
        $normalizedEmail = strtolower(trim($email));

        $user = User::where('email', $normalizedEmail)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new AuthenticationException('Invalid credentials provided.');
        }

        $token = $user->createToken(
            name: 'auth_token',
            abilities: ['role:' . $user->role->value],
            expiresAt: now()->addHours($expiresInHrs)
        )->plainTextToken;

        return [
            'user' => $this->getProfile($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Revoke tokens for the authenticated user.
     */
    public function logout(User $user, bool $revokeAll = false): void
    {
        if ($revokeAll) {
            $user->tokens()->delete();
            return;
        }

        $user->currentAccessToken()->delete();
    }

    /**
     * Retrieve the user profile payload.
     */
    public function getProfile(User $user): array
    {
        return $user->only(['id', 'name', 'email', 'phone', 'role']);
    }
}
