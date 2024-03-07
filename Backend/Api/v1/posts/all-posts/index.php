<?php
# we need a JWT token for api       -----(important)


require "../../../../bootstrap/init.php";

use App\Utilities\Response;
use App\Services\BaseModel;
use App\Utilities\CacheUtility;

# check request method         is (GET)?
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    # start caching service
    CacheUtility::startCaching();
    # connect to database
    $connection = new BaseModel('posts');
    # return posts data in $postDataResult variable
    $postDataResult = $connection->getAll();
    # Response for data result
    echo Response::respond($postDataResult, Response::HTTP_OK);
    # end caching service
    CacheUtility::endCaching();
    die();
} else {
    Response::respondAndDie(["درخواست نامعتبر است"], Response::HTTP_BAD_REQUEST);
}
