<?php

namespace Bilfeldt\RequestLogger\Middleware;

use Bilfeldt\RequestLogger\RequestLoggerFacade;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$drivers)
    {
        $request->enableLog(...$drivers);

        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $request->server('REQUEST_TIME_FLOAT');
        $duration = $startTime ? floor((microtime(true) - $startTime) * 1000) : null;
        $memory = memory_get_peak_usage(true);

        if ($this->shouldLog($request, $response)) {
            $request->enableLog();
        }

        foreach (array_unique($request->attributes->get('log', [])) as $driver) {
            RequestLoggerFacade::driver($driver)->log(
                $request,
                $response,
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
