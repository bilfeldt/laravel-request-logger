<?php

namespace Bilfeldt\RequestLogger\Listeners;

use Bilfeldt\RequestLogger\RequestLoggerFacade;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Symfony\Component\HttpFoundation\Response;

/**
 * This class is highly inspired by the Laravel Telescope Request Watcher.
 *
 * @see https://github.com/laravel/telescope/blob/master/src/Watchers/RequestWatcher.php
 */
class LogRequest
{
    public function handle(RequestHandled $event)
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $event->request->server('REQUEST_TIME_FLOAT');
        $duration = $startTime ? floor((microtime(true) - $startTime) * 1000) : null;
        $memory = memory_get_peak_usage(true);

        if ($this->shouldLog($event->request, $event->response)) {
            $event->request->enableLog();
        }

        foreach (array_unique($event->request->attributes->get('log', [])) as $driver) {
            RequestLoggerFacade::driver($driver)->log(
                $event->request,
                $event->response,
                $duration,
                $memory
            );
        }
    }

    protected function shouldLog(Request $request, Response $response): bool
    {
        if ($this->ignoredPath($request)) {
            return false;
        }

        if ($this->disabledRobot($request)) {
            return false;
        }

        if ($this->enabledMethod($request)) {
            return true;
        }

        if ($this->enabledStatus($response)) {
            return true;
        }

        return false;
    }

    protected function ignoredPath(Request $request): bool
    {
        return $request->is(config('request-logger.ignore_paths', []));
    }

    protected function disabledRobot(Request $request): bool
    {
        if (! config('request-logger.disable_robots_tracking')) {
            return false;
        }

        return (new CrawlerDetect())->isCrawler();
    }

    protected function enabledMethod(Request $request): bool
    {
        foreach (config('request-logger.log_methods') as $method) {
            if ($request->isMethod($method)) {
                return true;
            }
        }

        return false;
    }

    protected function enabledStatus(Response $response): bool
    {
        foreach (config('request-logger.log_statuses') as $status) {
            if (Str::is($status, $response->getStatusCode())) {
                return true;
            }
        }

        return false;
    }
}
