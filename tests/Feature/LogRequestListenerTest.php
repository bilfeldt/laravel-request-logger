<?php

namespace Bilfeldt\RequestLogger\Tests\Feature;

use Bilfeldt\RequestLogger\Listeners\LogRequest;
use Bilfeldt\RequestLogger\Tests\TestCase;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Event;

class LogRequestListenerTest extends TestCase
{
    public function test_listens_for_request_handled_event()
    {
        Event::fake();

        Event::assertListening(
            RequestHandled::class,
            LogRequest::class
        );
    }
}
