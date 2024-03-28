<?php

namespace App\Helpers;

use App\Exceptions\ConfigFileNotFoundException;

class Config
{
    public static function getFileContents(string $fileName)
    {
        $filePath = realpath(__DIR__ . '/../configs/' . "$fileName" . '.php');
        if (!$filePath) {
            throw new ConfigFileNotFoundException();
        }
        $fileContents = require $filePath;
        return $fileContents;
    }
    public static function get(string $fileName, string $key = null)
    {
        $fileContents = self::getFileContents($fileName);
        if (is_null($key)) return $fileContents;
        return $fileContents[$key] ?? null;
    }
}
