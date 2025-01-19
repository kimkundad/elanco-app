<?php

namespace App\Http\Utils;

use Illuminate\Support\Str;

class ArrayKeyConverter
{
    /**
     * Convert array keys to snake_case.
     *
     * @param array $data
     * @return array
     */
    public static function convertToSnakeCase(array $data): array
    {
        $snakeCaseData = [];
        foreach ($data as $key => $value) {
            $snakeCaseData[Str::snake($key)] = $value;
        }
        return $snakeCaseData;
    }
}
