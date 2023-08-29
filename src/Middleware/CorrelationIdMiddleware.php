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
        $correlationId = $this->getCorrelationId($request);

        $request->headers->set('Correlation-ID', $correlationId, true);

        $response = $next($request);

        $response->headers->set('Correlation-ID', $correlationId, true); // This is available on all Response types whereas $response->header() is only available in \Illuminate\Http\Response

        return $response;
    }

    private function getCorrelationId(Request $request): string
    {
        return $request->getUniqueId();
    }
}
