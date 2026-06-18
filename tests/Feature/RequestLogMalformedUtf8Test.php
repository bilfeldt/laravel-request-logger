<?php

namespace Bilfeldt\RequestLogger\Tests\Feature;

use Bilfeldt\RequestLogger\Models\RequestLog;
use Bilfeldt\RequestLogger\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequestLogMalformedUtf8Test extends TestCase
{
    public function test_malformed_utf8_in_payload_does_not_throw_and_is_saved()
    {
        // Lone continuation byte (0x80) and a truncated multibyte sequence —
        // the kind of bytes sent by malformed/malicious requests such as the
        // CVE-2024-4577 "php-cgi.exe" scanner probes. Before the fix this threw
        // Illuminate\Database\Eloquent\JsonEncodingException on save.
        $request = Request::create('/orders', 'POST', [
            'cgi' => "bad\x80\xFFvalue",
            'nested' => ['key' => "\xC3\x28"],
        ]);

        (new RequestLog())->log($request, new Response('{}', 200, ['Content-Type' => 'application/json']));

        $log = RequestLog::query()->latest('id')->first();

        $this->assertNotNull($log);
        $this->assertTrue(mb_check_encoding($log->payload['cgi'], 'UTF-8'));
        $this->assertTrue(mb_check_encoding($log->payload['nested']['key'], 'UTF-8'));
        $this->assertNotFalse(json_encode($log->payload));
    }

    public function test_malformed_utf8_in_headers_does_not_throw_and_is_saved()
    {
        $request = Request::create('/orders', 'GET');
        $request->headers->set('X-Custom', "head\x80er");

        (new RequestLog())->log($request, new Response('{}', 200, ['Content-Type' => 'application/json']));

        $log = RequestLog::query()->latest('id')->first();

        $this->assertNotNull($log);
        $this->assertTrue(mb_check_encoding($log->headers['x-custom'][0], 'UTF-8'));
        $this->assertNotFalse(json_encode($log->headers));
    }

    public function test_valid_utf8_payload_is_left_intact()
    {
        $request = Request::create('/orders', 'POST', ['name' => 'éàü 日本語']);

        (new RequestLog())->log($request, new Response('{}', 200, ['Content-Type' => 'application/json']));

        $log = RequestLog::query()->latest('id')->first();

        $this->assertSame('éàü 日本語', $log->payload['name']);
    }
}
