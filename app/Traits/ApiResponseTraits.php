<?php

declare(strict_types=1);

namespace App\Traits;

use App\Libs\ApiResponse;

/**
 * @property-read \App\Libs\ApiResponse $apiResponse
 */
trait ApiResponseTraits
{
    public function __get(string $name): mixed
    {
        if ($name === 'apiResponse') {
            return ApiResponse::class;
        }

        if (is_callable(['parent', '__get'])) {
            return parent::__get($name);
        }

        throw new \Exception("Property {$name} does not exist.");
    }
}
