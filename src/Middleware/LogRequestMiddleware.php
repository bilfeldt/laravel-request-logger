<?php

namespace Bilfeldt\RequestLogger\Middleware;

use Closure;
use Illuminate\Http\Request;
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

        if ($header = config('request-logger.header')) {
            $response->headers->set($header, $requestId, true); // This is available on all Response types whereas $response->header() is only available in \Illuminate\Http\Response
        }

        return $response;
    }
}
