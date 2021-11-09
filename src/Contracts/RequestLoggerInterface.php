<?php

namespace Bilfeldt\RequestLogger\Contracts;

use Illuminate\Http\Request;

interface RequestLoggerInterface
{
    /**
     * @param  Request  $request
     * @param  \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response  $response
     * @param  int|null  $duration
     * @param  int|null  $memory
     */
    public function log(
        Request $request,
        $response,
        ?int $duration = null,
        ?int $memory = null
    ): void;
}
