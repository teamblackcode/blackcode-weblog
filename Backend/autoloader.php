<?php 
function autoLoader($className)
{
    $fileName = __DIR__ . "/$className.php";
    if (!file_exists($fileName) && is_readable($fileName))
        die('کلاس مورد نظر یافت نشد');
    require $fileName;
}
spl_autoload_register('autoLoader');
