<?php

namespace Tests\Unit;

use App\Contracts\FileUploaderInterface;
use App\Exceptions\FileLoadErrorException;
use App\Exceptions\InvalidFileException;
use App\Exceptions\InvalidFileSizeException;
use App\Exceptions\InvalidFolderException;
use App\Utilities\FileUploader;
use PHPUnit\Framework\TestCase;

class FileUploaderTest extends TestCase
{
    private $folderName = 'posts';
    public function testFileUploaderImplementFileUploaderInterface()
    {
        $fileUploader = new FileUploader($this->folderName,$this->fakeData());
        $this->assertInstanceOf(FileUploaderInterface::class, $fileUploader);
    }

    public function testItShouldeBeReturnExceptionWhenInvalidFileName()
    {
        $this->expectException(InvalidFileException::class);
        new FileUploader($this->folderName, $this->fakeData(['name'=>'test.mp3']));
    }

    public function testItShouldeBeReturnExceptionWhenInvalidFileSize()
    {
        $this->expectException(InvalidFileSizeException::class);
        new FileUploader($this->folderName, $this->fakeData(['size'=>14520064]));
    }

    public function testItShouldeBeReturnExceptionWhenFoundErrorInFileLoading()
    {
        $this->expectException(FileLoadErrorException::class);
        new FileUploader('posts', $this->fakeData(['error'=>1]));
    }

    public function testItShouldeBeReturnExceptionWhenNotFoundFileName()
    {
        $this->expectException(InvalidFolderException::class);
        new FileUploader('asdjcsjo', $this->fakeData());

    }

    private function fakeData(array $options = [])
    {
        return array_merge([
            'name'=>'test.png',
            'error'=>4520064,
            'size'=>5
        ], $options);
    }
}
