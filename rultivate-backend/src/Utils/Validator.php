<?php
namespace Rultivate\Utils;

class Validator
{
    public static function required(array $data, array $fields): array
    {
        $missing = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $missing[] = $field;
            }
        }
        return $missing;
    }
}
