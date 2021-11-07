<?php

namespace Bilfeldt\RequestLogger;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Bilfeldt\RequestLogger\RequestLogger
 */
class RequestLoggerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return RequestLogger::class;
    }
}
