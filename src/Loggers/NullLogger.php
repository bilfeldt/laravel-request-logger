<?php

namespace Bilfeldt\RequestLogger\Loggers;

use Bilfeldt\RequestLogger\Contracts\RequestLoggerInterface;
use Illuminate\Http\Request;

class NullLogger implements RequestLoggerInterface
{
    public function log(Request $request, $response, ?int $time = null, ?int $memory = null): void
    {
        // Intentionally left blank.
    }
}
