<?php

namespace Bilfeldt\RequestLogger\Tests\Unit;

use Bilfeldt\RequestLogger\Middleware\CorrelationIdMiddleware;
use Bilfeldt\RequestLogger\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CorrelationIdMiddlewareTest extends TestCase
{
    public function test_adds_request_header(): void
    {
        $request = new Request();

        (new CorrelationIdMiddleware())->handle($request, function ($request) {

            $this->assertTrue($request->headers->has('Correlation-ID'));
            $this->assertEquals($request->getUniqueId(), $request->header('Correlation-ID'));

            return new Response();
        });
    }

    public function test_adds_response_header(): void
    {
        $request = new Request();

        $response = (new CorrelationIdMiddleware())->handle($request, function ($request) {
            return new Response();
        });

        $this->assertTrue($response->headers->has('Correlation-ID'));
        $this->assertEquals($request->getUniqueId(), $request->header('Correlation-ID'));
    }
}
