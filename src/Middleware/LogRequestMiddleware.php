<?php

namespace Bilfeldt\RequestLogger\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

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

        $requestId = $request->getUniqueId();

        if ($context = config('request-logger.log_context')) {
            Log::withContext([
                $context => $requestId,
            ]);
        }

        $response = $next($request);

        // Headers are available to Response types whereas $request->header() is only available in \Illuminate\Http\Response
        foreach (config('request-logger.headers') as $header) {
            if ($header_name = Arr::get($header, 'name')) {
                // key 'value' can be a \Closure or any other type of value
                $response->headers->set($header_name, value(Arr::get($header, 'value')), true);
            }
        }

        return $response;
    }
}
