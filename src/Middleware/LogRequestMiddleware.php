<?php

namespace Bilfeldt\RequestLogger\Middleware;

use Closure;
use Illuminate\Http\Request;

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
}
