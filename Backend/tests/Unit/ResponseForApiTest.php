<?php

namespace Tests\Unit;

use App\Utilities\Response;
use PHPUnit\Framework\TestCase;

class ResponseForApiTest extends TestCase
{
    public function testRespondMethodThatReturnsResponseForApiRequest()
    {
        $responseResult = Response::respond(['name'=>'test'], Response::HTTP_OK);
        $this->assertJson($responseResult);
        $this->assertEquals(Response::HTTP_OK, json_decode($responseResult, true)['http_status']);
        $this->assertEquals('OK', json_decode($responseResult, true)['http_message']);
    }
}
