<?php

namespace Bilfeldt\RequestLogger\Tests;

use Bilfeldt\CorrelationId\CorrelationIdServiceProvider;
use Bilfeldt\RequestLogger\Middleware\LogRequestMiddleware;
use Bilfeldt\RequestLogger\RequestLoggerServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Bilfeldt\\RequestLogger\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            RequestLoggerServiceProvider::class,
            CorrelationIdServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../database/migrations/create_request_logs_table.php.stub';
        $migration->up();
    }

    protected function defineRoutes($router)
    {
        $router->get('/', static fn () => 'Test!')->middleware(LogRequestMiddleware::class)->name('test');
    }
}
