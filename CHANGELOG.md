# Changelog

All notable changes to `laravel-request-logger` will be documented in this file.

## Upgrade guides

### 1.* => 2.*

No breaking changes. The only changes are to the development dependencies used for testing and then the minimum Laravel and PHP requirements.

## Changes

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
