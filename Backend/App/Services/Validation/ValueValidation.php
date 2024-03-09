<?php

namespace App\Services\Validation;

class ValueValidation
{
    public static function isValidArrayArguments($value)
    {
        foreach ($value as $item) {
            if (empty($item) || is_null($item))
                return;
        }
        return true;
    }
}
