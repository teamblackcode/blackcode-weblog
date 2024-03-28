<?php

namespace Tests\Unit;

use App\Exceptions\ConfigFileNotFoundException;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testGetFileContentsMethodReturnsArray()
    {
        $config = Config::getFileContents('database');
        $this->assertIsArray($config);
    }

    public function testItThrowExceptionIfFileNotFound()
    {
        $this->expectException(ConfigFileNotFoundException::class);
        Config::getFileContents('mahdi');
    }

    public function testGetMethodReturnsValidArray()
    {
        $config = Config::get('database', 'pdo_testing');
        $expectedData = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'blackcodeweblog_testing',
            'driver' => 'root',
            'driver' => '',
        ];
        $this->assertEquals($expectedData, $config);
    }
}
