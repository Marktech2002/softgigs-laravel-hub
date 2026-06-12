<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Enums\HttpStatusCode;
use App\Traits\ApiResponseTraits;

class AuthenticationException extends Exception
{
    use ApiResponseTraits;

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return self::unauthorized($this->getMessage());
    }
}
