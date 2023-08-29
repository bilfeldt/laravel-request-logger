<?php

namespace Bilfeldt\RequestLogger\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorrelationIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Correlation-ID', $request->getUniqueId(), true); // This is available on all Response types whereas $request->header() is only available in \Illuminate\Http\Response

        return $response;
    }
}
