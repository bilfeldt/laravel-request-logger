<?php

namespace Bilfeldt\RequestLogger\Tests\Feature;

use Bilfeldt\RequestLogger\ArrayLogger;
use Bilfeldt\RequestLogger\Tests\TestCase;

class LogHandlerTest extends TestCase
{
    public function test_logs_are_recieved_by_terminatable_middleware()
    {
        $logger = resolve(ArrayLogger::class);

        $this->assertEmpty($logger->getLogs());

        $this
            ->get(route('test'))
            ->assertOk();

        $logs = $logger->getLogs();

        $this->assertNotEmpty($logs);
        $this->assertCount(1, $logs);

        $this->assertArrayHasKey('uuid', $logs[0]);
        $this->assertArrayHasKey('correlation_id', $logs[0]);
        $this->assertArrayHasKey('client_request_id', $logs[0]);
        $this->assertArrayHasKey('ip', $logs[0]);
        $this->assertArrayHasKey('session', $logs[0]);
        $this->assertArrayHasKey('middleware', $logs[0]);
        $this->assertArrayHasKey('method', $logs[0]);
        $this->assertArrayHasKey('route', $logs[0]);
        $this->assertArrayHasKey('path', $logs[0]);
        $this->assertArrayHasKey('status', $logs[0]);
        $this->assertArrayHasKey('headers', $logs[0]);
        $this->assertArrayHasKey('payload', $logs[0]);
        $this->assertArrayHasKey('response_headers', $logs[0]);
        $this->assertArrayHasKey('response_body', $logs[0]);
        $this->assertArrayHasKey('duration', $logs[0]);
        $this->assertArrayHasKey('memory', $logs[0]);

        $this->assertEquals($logs[0]['method'], 'GET');
        $this->assertEquals($logs[0]['route'], 'test');
        $this->assertEquals($logs[0]['path'], '/');
        $this->assertEquals($logs[0]['status'], 200);
    }
}
