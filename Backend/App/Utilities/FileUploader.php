<?php

namespace App\Utilities;

use App\Contracts\FileUploaderInterface;
use App\Exceptions\FileLoadErrorException;
use App\Exceptions\InvalidFileException;
use App\Exceptions\InvalidFileSizeException;
use App\Exceptions\InvalidFolderException;
use Exception;

class FileUploader implements FileUploaderInterface
{
    private $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
    private $targetFolderName;
    private $fileDataArray;

    public function __construct($targetFolderName, $fileDataArray)
    {
        if (!$this->isValidFolderName($targetFolderName)) {
            throw new InvalidFolderException('فولدر نامعتبر است');
        }
        if (!$this->isValidFileName($fileDataArray['name'])) {
            throw new InvalidFileException('فایل نامعتبر است');
        }
        if (!$this->isValidFileSize($fileDataArray['size'])) {
            throw new InvalidFileSizeException('حداکثر حجم فایل ۱۰ مگابایت میتواند باشد');
        }
        if (!$this->isNotExistError($fileDataArray['error'])) {
            throw new FileLoadErrorException('خطا در بارگذاری فایل');
        }
        $this->targetFolderName = $this->generateTargetFolderPath($targetFolderName);
        $this->fileDataArray = $fileDataArray;
    }

    public function upload()
    {
        $currentFileName = $this->fileDataArray['name'];
        $uploadedFileFullPath = $this->targetFolderName . '\\' . $currentFileName;
        if (move_uploaded_file($this->fileDataArray['tmp_name'], $uploadedFileFullPath)) {
            return "فایل با موفقیت بارگذاری شد";
        }
    }

    private function isValidFileName(string $name)
    {
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        if (!in_array($extension, $this->allowedExtensions))
            return false;
        return true;
    }

    private function isValidFileSize(int $size)
    {
        $fileSize = ($size / 1024) / 1024;
        if ($fileSize > 10) {
            return;
        }
        return true;
    }

    private function isValidFolderName(string $name)
    {
        if (!is_dir($this->generateTargetFolderPath($name)))
            return false;
        return true;
    }

    private function isNotExistError(int $error)
    {
        if ($error == 1)
            return false;
        return true;
    }

    private function generateTargetFolderPath($folderName)
    {
        return realpath(__DIR__ . '../../../uploads/' . $folderName . '/');
    }
}
