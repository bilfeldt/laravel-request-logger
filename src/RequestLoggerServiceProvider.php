<?php

namespace Bilfeldt\RequestLogger;

use Bilfeldt\RequestLogger\Commands\RequestLogPruneCommand;
use Bilfeldt\RequestLogger\Middleware\LogRequestMiddleware;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
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
            ->hasCommand(RequestLogPruneCommand::class);
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

        Response::macro('getLoggableContent', function (): array {
            $content = $this->getContent();

            if (is_string($content)) {
                if (is_array(json_decode($content, true)) &&
                    json_last_error() === JSON_ERROR_NONE) {
                    return intdiv(mb_strlen($content), 1000) <= 64
                        ? Arr::replaceParameters(json_decode($content, true), []) // TODO: Insert parameter that must be replaced
                        : ['Purged By bilfeldt/laravel-request-logger'];
                }

                if (Str::startsWith(strtolower($this->headers->get('Content-Type')), 'text/plain')) {
                    return intdiv(mb_strlen($content), 1000) <= 64 ? [$content] : ['Purged By bilfeldt/laravel-request-logger'];
                }
            }

            if ($this instanceof RedirectResponse) {
                return ['Redirected to '.$this->getTargetUrl()];
            }

            if ($this->getOriginalContent() instanceof View) {
                return [
                    'view' => $this->getOriginalContent()->getPath(),
                    //'data' => $this->extractDataFromView($this->getOriginalContent()),
                ];
            }

            return ['HTML Response'];
        });

        Arr::macro('replaceParameters', function (array $array, array $hidden, string $value = '********'): array {
            foreach ($hidden as $parameter) {
                if (Arr::get($array, $parameter)) {
                    Arr::set($array, $parameter, '********');
                }
            }

            return $array;
        });
    }
}
