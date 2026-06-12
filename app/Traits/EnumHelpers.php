<?php

declare(strict_types=1);

namespace App\Traits;

trait EnumHelpers
{
    /**
     * Returns an array of all enum values.
     */
    public static function toArray(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    /**
     * Returns a comma-separated string for simple validation rules.
     * Note: Laravel also provides Rule::enum() which is preferable.
     */
    public static function inValidationRule(array $exclude = []): string
    {
        return implode(',', self::values($exclude));
    }

    /**
     * Returns an array of values, excluding specified ones.
     */
    public static function values(array $exclude = []): array
    {
        return array_values(array_filter(
            array_column(self::cases(), 'value'),
            fn ($value) => ! in_array($value, $exclude, true)
        ));
    }
}
