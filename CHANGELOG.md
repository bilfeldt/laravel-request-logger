# Changelog

All notable changes to `laravel-request-logger` will be documented in this file.

## Upgrade guides

### 2.* => 3.*

- Because the package now supports both `Correlation-ID` and `Request-ID` then two new database columns needs to be inserted into the `request_logs` table. See the table migration stub for the required changes.
- The function `aggregate($request, $response, $date)` has been removed from the `RequestLog` model. If you are using this function instead then use the [`bilfeldt/laravel-route-statistics`](https://packagist.org/packages/bilfeldt/laravel-route-statistics) package for aggregated statistical logging if needed.
- The config `log_context` has been removed. If you are using this config to add Log context, then apply the `LogContextMiddleware` from the now required package [`bilfeldt/laravel-correlation-id`](https://packagist.org/packages/bilfeldt/laravel-correlation-id) package for the relevant routes.
- The config `header` has been removed. If you are using this config to add the unique request id to the response in the `Request-ID` headerm then do so using your own middleware, or consider (recommended) to instead set the `Correlation-ID` header using the `CorrelationIdMiddleware` from the now required package [`bilfeldt/laravel-correlation-id`](https://packagist.org/packages/bilfeldt/laravel-correlation-id) package for the relevant routes.

### 1.* => 2.*

No breaking changes. The only changes are to the development dependencies used for testing and then the minimum Laravel and PHP requirements.

## Changes

### 3.3.0 - 2024-05-07

- Add config for which model to use in the `RequestLog` models `user()` relationship

### 3.2.0

- Add Laravel 11 compatibility

### 3.1.0

- Add PHP 8.3 compatibility

### 3.0.0

- Rely on the [`bilfeldt/laravel-correlation-id`](https://packagist.org/packages/bilfeldt/laravel-correlation-id) package for request and response headers and unique id.
- Log both `Correlation-ID` header and the unique _Request ID_.
- Remove dependency on `spatie/laravel-package-tools`
- Require PHP 8.2

### 2.2.0 - 2023-08-29

- Add middleware for adding 'Correlation-ID' header to responses

### 2.1.0 - 2023-07-24

- Add support for PHP 8.1

### 2.0.0 - 2023-05-01

- Add support for PHP 8.2
- Minimum PHP requirement 8.2
- Add support for Laravel 10.*
- Minimum Laravel requirement 10.0

### v1.1.0 - 2022-01-28

- Add Laravel 9 support
- Add PHP 8.1 support

### v1.0.2 - 2022-01-11

- Exclude the parameter api_token per default from all logs (added to config) #17

### v1.0.1 - 2021-11-15

- Fix issue when route is undefined: By @woenel in #15

### 1.0.0 - 2021-11-12

- initial release
