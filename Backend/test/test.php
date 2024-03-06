<?php

include "../bootstrap/init.php";
use App\Services\BaseModel;

$test = new BaseModel('province');
$result = $test->find(5);
var_dump($result);