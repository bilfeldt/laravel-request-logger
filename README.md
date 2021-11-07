# Flexible and extendable logging of Laravel application request and responses

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bilfeldt/laravel-request-logger.svg?style=flat-square)](https://packagist.org/packages/bilfeldt/laravel-request-logger)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/bilfeldt/laravel-request-logger/run-tests?label=tests)](https://github.com/bilfeldt/laravel-request-logger/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/bilfeldt/laravel-request-logger/Check%20&%20fix%20styling?label=code%20style)](https://github.com/bilfeldt/laravel-request-logger/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/bilfeldt/laravel-request-logger.svg?style=flat-square)](https://packagist.org/packages/bilfeldt/laravel-request-logger)

Zero configuration logging of Requests and Responses to database or custom drivers in Laravel applications - no more issues debugging customer support requests.

## Installation

You can install the package via composer:

```bash
composer require bilfeldt/laravel-request-logger
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Bilfeldt\RequestLogger\RequestLoggerServiceProvider" --tag="laravel-request-logger-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Bilfeldt\RequestLogger\RequestLoggerServiceProvider" --tag="laravel-request-logger-config"
```

## Usage

It is possible to enable logging of all or some requests conditionally using one of the approaches below.

### Enable log via middleware (Recommended)

This package comes with a convenient middleware that can be used to enable logging of requests.

Start by adding the middleware to the `$routeMiddleware` array in `app/Http/Kernel.php`:

```php
// Within App\Http\Kernel class...

protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
    'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'requestlog' => \Bilfeldt\RequestLogger\Middleware\LogRequestMiddleware::class, // <----- Added here
    'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
];
```

and then simply register the middleware on the routes (or route groups) you wish to log:

```php
Route::middleware('requestlog')->get('/', function () {
    return 'Hello World';
});
```

### Enable via config file

The config file includes some convenient settings for enabling logging of all or some requests:

```php
// Enable all requests:
'log_methods' => ['*'],

// or enable all server errors
'log_statuses' => ['5**'],
```

### Enable log via request

This package adds a macro on the `Request` class making it possible to enable logging directly from the request:

```php
/**
 * Index posts.
 *
 * @param  Request  $request
 * @return Response
 */
public function index(Request $request)
{
    $request->enableLog();

    //
}
```

### Drivers

This package implements the [Laravel Manager Class](https://inspector.dev/how-to-extend-laravel-with-driver-based-services/) making it possible to easily register custom drivers either in your application or by third party packages.

An `example` driver can be specified as middleware parameters:

```php
Route::middleware('log:example')->get('/', function () {
    return 'Hello World';
});
```

or via request macro:

```php
$request->enableLog('example');
```

### Unique Request UUID

This package adds a macro `getUniqueId()` for the `Request` which generates a unique request UUID that will be saved to the logs and that can be included as [Global Log Context](https://laravel.com/docs/8.x/logging#contextual-information) which will pass it onto any application logging or error reporting. This id will per default also be added as a custom response header.

**This is an extremely helpful trick when debugging customer requests as both customer, application logs, reported errors and request logs (this package) now all include a single common UUID!**

```php
$request->getUniqueId(); // Example: 94d0e2d6-4cc6-449c-9140-80bca47d29b4
```

## Pruning

The number of logged requests can quickly grow if you are not pruning the logs regularly. In order to keep the logs manageable, you can use the `prune` command to remove old logs as [described in the Laravel Docs](https://laravel.com/docs/8.x/eloquent#pruning-models):

```php
$schedule->command('model:prune', [
    '--model' => [\Bilfeldt\RequestLogger\Models\RequestLog::class],
])->daily();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Anders Bilfeldt](https://github.com/bilfeldt)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
