<?php

namespace Bilfeldt\RequestLogger\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

interface RequestLoggerInterface
{
    public function log(
        Request $request,
        $response, // TODO: Response can be either Illuminate\Http\Response|Illuminate\Http\RedirectResponse
        ?int $time = null,
        ?int $memory = null
    ): void;
}
