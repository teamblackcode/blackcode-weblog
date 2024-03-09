<?php
require "../../../bootstrap/init.php";
# we need a JWT token for create & delete & update post    ------(important)
use App\Utilities\Response;
use App\Services\BaseModel;
use App\Utilities\CacheUtility;

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestBody = json_decode(file_get_contents('php://input'), true);
# check request method 
if ($requestMethod == 'GET') {
    # save post_id from superglobal array (GET)
    $postId = (int)$_GET['post_id'];
    # start caching service
    CacheUtility::startCaching();
    # connect to database
    $connection = new BaseModel('posts');
    # return a post data in $postDataResult variable    (1 post)
    $postDataResult = $connection->find($postId);
    # Response for data result
    echo Response::respond($postDataResult, Response::HTTP_OK);
    # end caching service
    CacheUtility::endCaching();
    die();
} else if ($requestMethod == 'POST') {
    # connect to database
    $connection = new BaseModel('posts');

    $postData = [
        "title" => $requestBody['title'],
        "content_text" => $requestBody['content_text'],
        "image_url" => $requestBody['image_url'],
        "author" => $requestBody['author'],
        "categorie_id" => $requestBody['categorie_id'],
        "status" => $requestBody['status']
    ];

    $createdPostResult = $connection->create($postData);
    if ($createdPostResult == 0)
        Response::respondAndDie(['خطا در ذخیره سازی پست! لطفا مقدار فیلد هارا درست وارد کنید'], Response::HTTP_BAD_REQUEST);
    Response::respondAndDie(['پست با موفقیت ایجاد شد'], Response::HTTP_CREATED);
} else if ($requestMethod == "DELETE") {
    $connection = new BaseModel('posts');
    $deletedPostResult = $connection->delete(['id' => $requestBody['post_id']]);
    if ($deletedPostResult == 0)
        Response::respondAndDie(['عملیات حذف با شکست مواجه شد'], Response::HTTP_NOT_FOUND);
    Response::respondAndDie(['پست مورد نظر حذف شد'], Response::HTTP_OK);
}
