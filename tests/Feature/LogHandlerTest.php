<?php

namespace Bilfeldt\RequestLogger\Tests\Feature;

use Bilfeldt\RequestLogger\Contracts\RequestLoggerInterface;
use Bilfeldt\RequestLogger\RequestLoggerFacade;
use Bilfeldt\RequestLogger\Tests\TestCase;
use Mockery\MockInterface;

class LogHandlerTest extends TestCase
{
    public function test_logs_are_recieved_by_terminatable_middleware()
    {
        $logger = $this->mock(RequestLoggerInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('log')->once();
        });

        RequestLoggerFacade::extend('mock', fn () => $logger);

        config()->set('request-logger.default', 'mock');

        $this
            ->get(route('test'))
            ->assertOk();
    }
}
