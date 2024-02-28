<?php

namespace Bilfeldt\RequestLogger\Traits;

use Bilfeldt\RequestLogger\RequestLoggerFacade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;

trait Loggable
{
    protected function getLoggableResponseContent(\Symfony\Component\HttpFoundation\Response $response): array
    {
        $content = $response->getContent();

        if (is_string($content)) {
            if (is_array(json_decode($content, true)) &&
                json_last_error() === JSON_ERROR_NONE) {
                return $this->contentWithinLimits($content)
                    ? $this->getFiltered(json_decode($content, true))
                    : ['Purged By bilfeldt/laravel-request-logger'];
            }

            if (Str::startsWith(strtolower($response->headers->get('Content-Type')), 'text/plain')) {
                return $this->contentWithinLimits($content) ? [$content] : ['purge' => 'bilfeldt/laravel-request-logger'];
            }
        }

        if ($response instanceof RedirectResponse) {
            return ['redirect' => $response->getTargetUrl()];
        }

        if ($response instanceof Response && $response->getOriginalContent() instanceof View) {
            return [
                'view' => $response->getOriginalContent()->getPath(),
                //'data' => $this->extractDataFromView($response->getOriginalContent()),
            ];
        }

        return ['html' => 'non-json'];
    }

    protected function contentWithinLimits(string $content): bool
    {
        return intdiv(mb_strlen($content), 1000) <= 64;
    }

    protected function getFiltered(array $data)
    {
        return $this->replaceParameters($data, RequestLoggerFacade::getFilters());
    }

    protected function replaceParameters(array $array, array $hidden, string $value = '********'): array
    {
        foreach ($hidden as $parameter) {
            if (Arr::get($array, $parameter)) {
                Arr::set($array, $parameter, '********');
            }
        }

        return $array;
    }

    protected function truncateToLength(?string $string, int $length = 255): ?string
    {
        if (! $string) {
            return $string;
        }

        $truncator = '...';

        if (mb_strwidth($string, 'UTF-8') <= $length) {
            return $string;
        }

        return Str::limit($string, $length - mb_strwidth($truncator, 'UTF-8'), $truncator);
    }
}
