<?php
namespace Rultivate\Utils;

class Sanitizer
{
    public static function cleanString(?string $value): ?string
    {
        return $value === null ? null : trim(strip_tags($value));
    }

    public static function cleanArray(array $data): array
    {
        return array_map(function ($value) {
            if (is_string($value)) {
                return self::cleanString($value);
            }
            if (is_array($value)) {
                return self::cleanArray($value);
            }
            return $value;
        }, $data);
    }
}
