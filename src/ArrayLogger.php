<?php

namespace Bilfeldt\RequestLogger;

use Bilfeldt\RequestLogger\Contracts\RequestLoggerInterface;
use Bilfeldt\RequestLogger\Traits\Loggable;
use Illuminate\Http\Request;

class ArrayLogger implements RequestLoggerInterface
{
    use Loggable;

    /**
     * Logs
     *
     * @var array<string, mixed>
     */
    protected array $logs = [];

    /** @inheritDoc */
    public function log(Request $request, $response, ?int $duration = null, ?int $memory = null): void
    {
        $this->logs[] = [
            'uuid' => $request->getUniqueId(),
            'correlation_id' => $this->truncateToLength($request->getCorrelationId()),
            'client_request_id' => $this->truncateToLength($request->getClientRequestId()),
            'ip' => $request->ip(),
            'session' => $request->hasSession() ? $request->session()->getId() : null,
            'middleware' => array_values(optional($request->route())->gatherMiddleware() ?? []),
            'method' => $request->getMethod(),
            'route' => $this->truncateToLength(optional($request->route())->getName() ?? optional($request->route())->uri()),
            'path' => $this->truncateToLength($request->path()),
            'status' => $response->getStatusCode(),
            'headers' => $this->getFiltered($request->headers->all()) ?: null,
            'payload' => $this->getFiltered($request->input()) ?: null,
            'response_headers' => $this->getFiltered($response->headers->all()) ?: null,
            'response_body' => $this->getLoggableResponseContent($response),
            'duration' => $duration,
            'memory' => round($memory / 1024 / 1024, 2),
        ];
    }

    /**
     * Get logs
     *
     * @return array<string, mixed>
     */
    public function getLogs(): array
    {
        return $this->logs;
    }
}
