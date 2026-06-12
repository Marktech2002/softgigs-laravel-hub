<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Enums\UserRoles;
use App\Traits\ApiResponseTraits;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    use ApiResponseTraits;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || $request->user()->role->value !== UserRoles::ADMIN->value) {
            return $this->apiResponse::accessDenied('Only administrators are allowed to perform this action.');
        }

        return $next($request);
    }
}
