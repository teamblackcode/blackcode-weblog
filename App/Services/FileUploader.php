<?php

namespace App\Services;

class FileUploader
{
    # save $_FILES data in $file variable
    private $file;
    # save target direction in $targetDir variable
    private $targetDir;
    # save and set maximum fiel size in $maxFileSize variable
    private $maxFileSize;
    # set and save allowed suffixs in $allowedSuffix variable
    private $allowedSuffix;
    # create this class by calling this class
    public function __construct($file, $targetDir, $maxFileSize, $allowedSuffix)
    {
        $this->file = $file;
        $this->targetDir = $targetDir;
        $this->maxFileSize = $maxFileSize;
        $this->allowedSuffix = $allowedSuffix;
    }
    # upload file function
    public function upload()
    {
        # check file error
        if ($this->file['error']) {
            die('اپلود فایل با خطا مواجه شد');
        }
        # check file size ($maxFileSize)
        if ($this->file['size'] > $this->maxFileSize) {
            die('حجم فایل بیشتر از حد مجاز است');
        }
        # check file suffix
        if (!in_array($this->file['type'], $this->allowedSuffix)) {
            die('نوع فایل مجاز نیست');
        }
        # create a new file name
        $newFileName = uniqid() . '_' . basename($this->file['name']);
        # create file path
        $targetFilePath = $this->targetDir . $newFileName;
        # check file uploaded to file
        if (move_uploaded_file($this->file['tmp_name'], $targetFilePath)) {
            die('فایل با موفقیت اپلود شد');
        }
        die('اپلود فایل با خطا مواجه شد');
    }
}