<?php

namespace Bilfeldt\RequestLogger\Loggers;

use Bilfeldt\RequestLogger\Contracts\RequestLoggerInterface;
use Bilfeldt\RequestLogger\Models\RequestLog;
use Illuminate\Http\Request;

class ModelLogger implements RequestLoggerInterface
{
    private RequestLog $model;

    public function __construct(RequestLog $model)
    {
        $this->model = $model;
    }

    public function log(Request $request, $response, ?int $time = null, ?int $memory = null): void
    {
        $this->model->log($request, $response, $time, $memory);
    }
}
