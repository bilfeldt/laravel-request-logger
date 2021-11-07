<?php

namespace Bilfeldt\RequestLogger\Listeners;

use Bilfeldt\RequestLogger\RequestLoggerFacade;
use Illuminate\Foundation\Http\Events\RequestHandled;

class LogRequest
{
    public function handle(RequestHandled $event)
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $event->request->server('REQUEST_TIME_FLOAT');
        $time = $startTime ? floor((microtime(true) - $startTime) * 1000) : null;
        $memory = memory_get_peak_usage(true);

        foreach (array_unique($event->request->attributes->get('log', [])) as $driver) {
            RequestLoggerFacade::driver($driver)->log(
                $event->request,
                $event->response,
                $time,
                $memory
            );
        }
    }
}
