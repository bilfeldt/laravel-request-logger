<?php

namespace Bilfeldt\RequestLogger\Tests\Unit;

use Bilfeldt\RequestLogger\Middleware\LogRequestMiddleware;
use Bilfeldt\RequestLogger\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LogRequestMiddlewareTest extends TestCase
{
    public function test_adds_default_driver()
    {
        $request = new Request();

        (new LogRequestMiddleware())->handle($request, function ($request) {
            return new Response();
        });

        $this->assertIsArray($request->attributes->get('log'));
        $this->assertContains(config('request-logger.default'), $request->attributes->get('log'));
    }

    public function test_adds_specified_drivers()
    {
        $request = new Request();

        (new LogRequestMiddleware())->handle($request, function ($request) {
            return new Response();
        }, 'driver1', 'driver2');

        $this->assertIsArray($request->attributes->get('log'));
        $this->assertContains('driver1', $request->attributes->get('log'));
        $this->assertContains('driver2', $request->attributes->get('log'));
    }
}
