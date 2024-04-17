<?php

use App\Utilities\FileUploader;
use App\Database\PDODatabaseConnection;
use App\Database\QueryBuilder;
use App\Helpers\Config;

require "./vendor/autoload.php";
function getConfig()
{
    return Config::get('database', 'pdo_testing');
}

$pdoConnection = new PDODatabaseConnection(getConfig());
$queryBuilder = new QueryBuilder($pdoConnection->connect());
if (isset($_POST['btn'])) {
    $file = $_FILES['file'];
    $uploader = new FileUploader('posts', $file);
    $uploader->upload();
    $queryBuilder
    ->table('images')
    ->create(data($uploader->getName()));
}
$images = $queryBuilder
    ->table('images')
    ->getAll(['image_url']);

function data($name)
{
    return [
        "image_url" => $name
    ];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="file" placeholder="file">
        <button name="btn">submit</button>
    </form>
    <?php foreach ($images as $image) : ?>
        <img src="./uploads/posts/<?= $image['image_url'] ?>" width="200" height="200" alt="">
    <?php endforeach; ?>

</body>

</html>