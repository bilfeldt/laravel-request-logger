<?php

namespace Bilfeldt\RequestLogger\Commands;

use Illuminate\Console\Command;

class PruneRequestLogsCommand extends Command
{
    public $signature = 'requestlog:prune';

    public $description = 'Prune any old request logs';

    public function handle(): int
    {
        return $this->call('model:prune', [
            '--model' => config('request-logger.drivers.model.class'),
        ]);
    }
}
