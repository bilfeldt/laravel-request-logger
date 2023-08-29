<?php

namespace Bilfeldt\RequestLogger\Tests\Unit;

use Bilfeldt\RequestLogger\Middleware\CorrelationIdMiddleware;
use Bilfeldt\RequestLogger\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CorrelationIdMiddlewareTest extends TestCase
{
    public function test_adds_response_header(): void
    {
        $this->assertEquals('Correlation-ID', config('request-logger.header'));

        $request = new Request();

        $response1 = (new CorrelationIdMiddleware())->handle($request, function ($request) {
            return new Response();
        });

        $this->assertTrue($response1->headers->has('Correlation-ID'));
        $this->assertEquals($request->getUniqueId(), $response1->headers->get('Correlation-ID'));
    }
}
