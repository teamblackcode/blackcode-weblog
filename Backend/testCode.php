<?php

use App\Utilities\FileUploader;

require "./vendor/autoload.php";

if (isset($_POST['btn'])) {
    $file = $_FILES['file'];
    // $uploader = new FileUploader('posts', $file);
    // $uploader->upload();
    var_dump($file);
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
</body>

</html>