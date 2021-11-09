<?php

namespace Bilfeldt\RequestLogger;

use Bilfeldt\RequestLogger\Contracts\RequestLoggerInterface;
use Illuminate\Http\Request;

class NullLogger implements RequestLoggerInterface
{
    /** @inheritDoc */
    public function log(Request $request, $response, ?int $duration = null, ?int $memory = null): void
    {
        // Intentionally left blank.
    }
}
