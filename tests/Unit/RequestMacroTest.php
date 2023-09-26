<?php

namespace Bilfeldt\RequestLogger\Tests\Unit;

use Bilfeldt\RequestLogger\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequestMacroTest extends TestCase
{
    public function test_request_macro_enable_log_registered()
    {
        $this->assertTrue((new Request())->hasMacro('enableLog'));
    }

    public function test_request_macro_enable_log()
    {
        $request = new Request();

        $this->assertFalse($request->attributes->has('log'));

        $request->enableLog();

        $this->assertTrue($request->attributes->has('log'));
        $this->assertContains(config('request-logger.default'), $request->attributes->get('log'));
        $this->assertNotContains('test-driver', $request->attributes->get('log'));

        $request->enableLog('test-driver');

        $this->assertContains('test-driver', $request->attributes->get('log'));
    }
}
