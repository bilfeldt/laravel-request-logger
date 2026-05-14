<?php

namespace Bilfeldt\RequestLogger\Tests\Feature;

use Bilfeldt\RequestLogger\Models\RequestLog;
use Bilfeldt\RequestLogger\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequestLogFilteringTest extends TestCase
{
    public function test_authorization_header_is_filtered_regardless_of_filter_casing()
    {
        config()->set('request-logger.filters', ['Authorization']);

        $request = Request::create('/orders', 'POST');
        $request->headers->set('Authorization', 'Bearer super-secret-token');

        (new RequestLog())->log($request, new Response('{}', 200, ['Content-Type' => 'application/json']));

        $log = RequestLog::query()->latest('id')->first();

        $this->assertNotNull($log->headers);
        $this->assertSame('********', $log->headers['authorization']);
        $this->assertStringNotContainsString('super-secret-token', json_encode($log->headers));
    }

    public function test_payload_filtering_remains_case_sensitive()
    {
        config()->set('request-logger.filters', ['password']);

        $request = Request::create('/login', 'POST', [
            'password' => 'secret',
            'Password' => 'kept-as-is',
        ]);

        (new RequestLog())->log($request, new Response('{}', 200, ['Content-Type' => 'application/json']));

        $log = RequestLog::query()->latest('id')->first();

        $this->assertSame('********', $log->payload['password']);
        $this->assertSame('kept-as-is', $log->payload['Password']);
    }
}
