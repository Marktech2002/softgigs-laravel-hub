<?php

declare(strict_types=1);

namespace App\Libs;

use App\Enums\HttpStatusCode;
use Illuminate\Http\JsonResponse;

/**
 * Class ApiResponse
 * Handles consistent API response formatting.
 */
class ApiResponse
{
    /**
     * General method to return a JSON response.
     *
     * @param  HttpStatusCode  $statusCode  HTTP status code.
     * @param  bool    $success  Success boolean.
     * @param  string  $message  Response message.
     * @param  mixed  $data  Response data.
     * @param  string|null  $error  Error message, if any.
     * @param  array  $errors  Array of validation or other errors.
     * @param  array  $headers  Additional headers for the response.
     */
    public static function json(
        HttpStatusCode $statusCode,
        bool $success,
        string $message,
        mixed $data = null,
        ?string $error = null,
        array $errors = [],
        array $headers = []
    ): JsonResponse {

        // Sanitize error messages to avoid leaking sensitive info
        if ($error && self::errorContainsSensitiveInfo((string) $error)) {
            $error = 'An internal error occurred. Please contact support.';

            $errors = array_map(function ($err) {
                return 'An internal error occurred. Please contact support.';
            }, $errors);
        }

        $response = [
            'success' => $success,
            'code' => $statusCode->value,
            'message' => $message,
            'data' => $data,
            'error' => $error,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String(),
        ];

        return response()->json($response, $statusCode->value, $headers);
    }

    /**
     * Return a successful JSON response.
     *
     * @param  string  $message  Response message.
     * @param  mixed  $data  Response data.
     * @param  array  $headers  Additional headers for the response.
     */
    public static function success(string $message, mixed $data = null, array $headers = []): JsonResponse
    {
        return self::json(HttpStatusCode::OK, true, $message, $data, null, [], $headers);
    }

    /**
     * Return a created JSON response.
     *
     * @param  string  $message  Response message.
     * @param  mixed  $data  Response data.
     * @param  array  $headers  Additional headers for the response.
     */
    public static function created(string $message, mixed $data = null, array $headers = []): JsonResponse
    {
        return self::json(HttpStatusCode::CREATED, true, $message, $data, null, [], $headers);
    }

    /**
     * Return a JSON response with an error status code.
     *
     * @param  string  $message  Response message.
     * @param  mixed  $data  Response data.
     * @param  string|null  $error  Error message, if any.
     * @param  array  $errors  Array of validation or other errors.
     * @param  array  $headers  Additional headers for the response.
     * @param  HttpStatusCode $statusCode HTTP Status Code
     */
    public static function error(
        string $message,
        mixed $data = null,
        ?string $error = null,
        array $errors = [],
        array $headers = [],
        HttpStatusCode $statusCode = HttpStatusCode::INTERNAL_SERVER_ERROR
    ): JsonResponse {
        return self::json($statusCode, false, $message, $data, $error, $errors, $headers);
    }

    /**
     * Return a JSON response with a 400 Bad Request status code.
     *
     * @param  string  $message  Response message.
     * @param  mixed  $data  Response data.
     * @param  string|null  $error  Error message, if any.
     * @param  array  $errors  Array of validation or other errors.
     * @param  array  $headers  Additional headers for the response.
     */
    public static function badRequest(
        string $message,
        mixed $data = null,
        ?string $error = null,
        array $errors = [],
        array $headers = []
    ): JsonResponse {
        return self::json(HttpStatusCode::BAD_REQUEST, false, $message, $data, $error, $errors, $headers);
    }

    /**
     * Return a JSON response with a 422 Unprocessable Entity status code.
     *
     * @param  string  $message  Response message.
     * @param  mixed  $data  Response data.
     * @param  string|null  $error  Error message, if any.
     * @param  array  $errors  Array of validation or other errors.
     * @param  array  $headers  Additional headers for the response.
     */
    public static function validationError(
        string $message,
        mixed $data = null,
        ?string $error = null,
        array $errors = [],
        array $headers = []
    ): JsonResponse {
        return self::json(HttpStatusCode::UNPROCESSABLE_ENTITY, false, $message, $data, $error, $errors, $headers);
    }

    /**
     * Return a JSON response with a 404 Not Found status code.
     *
     * @param  string  $message  Response message.
     * @param  mixed  $data  Response data.
     * @param  string|null  $error  Error message, if any.
     * @param  array  $errors  Array of validation or other errors.
     * @param  array  $headers  Additional headers for the response.
     */
    public static function notFound(
        string $message,
        mixed $data = null,
        ?string $error = null,
        array $errors = [],
        array $headers = []
    ): JsonResponse {
        return self::json(HttpStatusCode::NOT_FOUND, false, $message, $data, $error, $errors, $headers);
    }

    /**
     * Return a JSON response with a 500 Internal Server Error status code.
     *
     * @param  string  $message  Response message.
     * @param  mixed  $data  Response data.
     * @param  string|null  $error  Error message, if any.
     * @param  array  $errors  Array of validation or other errors.
     * @param  array  $headers  Additional headers for the response.
     */
    public static function serverError(
        string $message,
        mixed $data = null,
        ?string $error = null,
        array $errors = [],
        array $headers = []
    ): JsonResponse {
        return self::json(HttpStatusCode::INTERNAL_SERVER_ERROR, false, $message, $data, $error, $errors, $headers);
    }

    /**
     * Return a JSON response with a 403 Forbidden status code (Access Denied).
     *
     * @param  string  $message  Response message.
     * @param  mixed  $data  Response data.
     * @param  string|null  $error  Error message, if any.
     * @param  array  $errors  Array of validation or other errors.
     * @param  array  $headers  Additional headers for the response.
     */
    public static function accessDenied(
        string $message,
        mixed $data = null,
        ?string $error = null,
        array $errors = [],
        array $headers = []
    ): JsonResponse {
        return self::json(HttpStatusCode::FORBIDDEN, false, $message, $data, $error, $errors, $headers);
    }

    /**
     * Return a JSON response with a 401 Unauthorized status code (Unauthenticated).
     *
     * @param  string  $message  Response message.
     * @param  mixed  $data  Response data.
     * @param  string|null  $error  Error message, if any.
     * @param  array  $errors  Array of validation or other errors.
     * @param  array  $headers  Additional headers for the response.
     */
    public static function unauthorized(
        string $message,
        mixed $data = null,
        ?string $error = null,
        array $errors = [],
        array $headers = []
    ): JsonResponse {
        return self::json(HttpStatusCode::UNAUTHORIZED, false, $message, $data, $error, $errors, $headers);
    }

    /**
     * Detect if the error message or trace contains sensitive details.
     */
    private static function errorContainsSensitiveInfo(string $error): bool
    {
        if (app()->environment('local') || config('app.debug')) {
            return false;
        }

        $combined = implode('|', config('security.sensitive_patterns', []));

        return $combined && (bool) preg_match("/{$combined}/i", $error);
    }
}
