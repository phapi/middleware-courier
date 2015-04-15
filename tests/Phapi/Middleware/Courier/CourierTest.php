<?php

namespace Phapi\Tests\Middleware\Courier;

use Phapi\Middleware\Courier\Courier;
use Phapi\Middleware\Courier\Output;
use PHPUnit_Framework_TestCase as TestCase;

require_once __DIR__ .'/TestAssets/Output.php';

/**
 * @coversDefaultClass \Phapi\Middleware\Courier\Courier
 */
class CourierTest extends TestCase {

    public function setUp()
    {
        Output::reset();
        /* ... */
    }

    public function tearDown()
    {
        Output::reset();
        /* ... */
    }

    /**
     * @runInSeparateProcess
     */
    public function testInvoke()
    {
        $request = \Mockery::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getMethod')->andReturn('GET');

        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');

        // Mock body and header
        $response->shouldReceive('getBody')->andReturn('this is the body');
        $response->shouldReceive('getHeaders')->andReturn([
            'X-Foo' => ['Bar'],
            'X-Rate-Limit-Remaining' => [11]
        ]);

        $response->shouldReceive('getReasonPhrase')->andReturn('Ok');
        $response->shouldReceive('getStatusCode')->andReturn('200');
        $response->shouldReceive('getProtocolVersion')->andReturn('1.1');

        $courier = new Courier();
        $courier($request, $response, function ($request, $response) {
            return $response;
        });

        $this->assertContains('HTTP/1.1 200 Ok', Output::$headers);
        $this->assertContains('X-Foo: Bar', Output::$headers);
        $this->assertContains('X-Rate-Limit-Remaining: 11', Output::$headers);
        $this->assertEquals('this is the body', Output::$body);
    }

    /**
     * @runInSeparateProcess
     */
    public function testWithoutReasonPhrase()
    {
        $request = \Mockery::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getMethod')->andReturn('GET');

        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');

        // Mock body and header
        $response->shouldReceive('getBody')->andReturn('this is the body');
        $response->shouldReceive('getHeaders')->andReturn([
            'X-Foo' => ['Bar'],
            'X-Rate-Limit-Remaining' => [11]
        ]);

        $response->shouldReceive('getReasonPhrase')->andReturn(null);
        $response->shouldReceive('getStatusCode')->andReturn('200');
        $response->shouldReceive('getProtocolVersion')->andReturn('1.1');

        $courier = new Courier();
        $courier($request, $response, function ($request, $response) {
            return $response;
        });

        $this->assertContains('HTTP/1.1 200', Output::$headers);
        $this->assertContains('X-Foo: Bar', Output::$headers);
        $this->assertContains('X-Rate-Limit-Remaining: 11', Output::$headers);
        $this->assertEquals('this is the body', Output::$body);
    }
}