<?php

namespace App\Utilities;

use App\Utilities\Response;

class CacheUtility
{
    # cache file path
    protected static $cacheFile;
    # check enbale or disable cache service
    protected static $cacheEnabled = CACHE_ENABLED;
    # set time for expire cache
    const EXPIRE_TIME = 10;

    public static function init()
    {
        self::$cacheFile = CACHE_DIR . md5($_SERVER['REQUEST_URI']) . '.json';
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            self::$cacheEnabled = 0;
        }
    }

    # check cache file exist
    public static function cacheExist()
    {
        return (file_exists(self::$cacheFile) && (time() - self::EXPIRE_TIME) < filemtime(self::$cacheFile));
    }

    # start caching
    public static function startCaching()
    {
        self::init();
        if (!self::$cacheEnabled)
            return;
        if (self::cacheExist()) {
            Response::setHeaders();
            readfile(self::$cacheFile);
            exit;
        }
        ob_start();
    }

    # end of caching
    public static function endCaching()
    {
        if (!self::$cacheEnabled)
            return;
        $cachedFile = fopen(self::$cacheFile, 'w');
        fwrite($cachedFile, ob_get_contents());
        fclose($cachedFile);
        ob_end_flush();
    }

    public static function flush()
    {
        $files = glob(CACHE_DIR, "*");
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file);
        }
    }
}
