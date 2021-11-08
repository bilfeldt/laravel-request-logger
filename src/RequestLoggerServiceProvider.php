<?php

namespace Bilfeldt\RequestLogger;

use Bilfeldt\RequestLogger\Commands\PruneRequestLogsCommand;
use Bilfeldt\RequestLogger\Middleware\LogRequestMiddleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RequestLoggerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-request-logger')
            ->hasConfigFile()
            ->hasMigration('create_request_logs_table')
            ->hasCommand(PruneRequestLogsCommand::class);
    }

    public function packageRegistered()
    {
        $this->app->register(EventServiceProvider::class);
    }

    public function packageBooted()
    {
        $this->registerMiddlewareAlias();

        $this->registerMacros();
    }

    private function registerMiddlewareAlias(): void
    {
        $this->app
            ->make(Router::class)
            ->aliasMiddleware('requestlog', LogRequestMiddleware::class);
    }

    private function registerMacros(): void
    {
        Request::macro('getUniqueId', function (): string {
            if (! $this->attributes->has('uuid')) {
                $this->attributes->set('uuid', (string) Str::orderedUuid());
            }

            return $this->attributes->get('uuid');
        });

        Request::macro('enableLog', function (string ...$drivers): Request {
            $loggers = $this->attributes->get('log', []);

            if (empty($drivers)) {
                $loggers[] = RequestLoggerFacade::getDefaultDriver();
            }

            foreach ($drivers as $driver) {
                $loggers[] = $driver;
            }

            $this->attributes->set('log', $loggers);

            return $this;
        });
    }
}
