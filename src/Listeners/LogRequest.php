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

    }
}
