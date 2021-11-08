<?php

namespace Bilfeldt\RequestLogger\Tests\Unit;

use Bilfeldt\RequestLogger\Middleware\LogRequestMiddleware;
use Bilfeldt\RequestLogger\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

class LogRequestMiddlewareTest extends TestCase
{
    function test_adds_default_driver()
    {
        $request = new Request();

        (new LogRequestMiddleware())->handle($request, function ($request) {
            return new Response();
        });

        $this->assertIsArray($request->attributes->get('log'));
        $this->assertContains(config('request-logger.default'), $request->attributes->get('log'));
    }

    function test_adds_specified_drivers()
    {
        $request = new Request();

        (new LogRequestMiddleware())->handle($request, function ($request) {
            return new Response();
        }, 'driver1', 'driver2');

        $this->assertIsArray($request->attributes->get('log'));
        $this->assertContains('driver1', $request->attributes->get('log'));
        $this->assertContains('driver2', $request->attributes->get('log'));
    }

    function test_adds_unique_uuid_to_response_header()
    {
        $this->assertEquals('Request-Id', config('request-logger.header'));

        $request = new Request();

        $response1 = (new LogRequestMiddleware())->handle($request, function ($request) {
            return new Response();
        });

        $this->assertFalse($response1->headers->has('test-header'));

        Config::set('request-logger.header', 'test-header');

        $response2 = (new LogRequestMiddleware())->handle($request, function ($request) {
            return new Response();
        });

        $this->assertTrue($response2->headers->has('test-header'));
        $this->assertEquals($request->getUniqueId(), $response2->headers->get('test-header'));
    }

    function test_adds_log_context()
    {
        // Test that $request->getUniqueId() is added to the global log context when config('request-logger.header')

        $this->markTestIncomplete();
    }
}
