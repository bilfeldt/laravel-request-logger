<?php

namespace Bilfeldt\RequestLogger\Commands;

use Illuminate\Console\Command;

class RequestLoggerCommand extends Command
{
    public $signature = 'laravel-request-logger';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
