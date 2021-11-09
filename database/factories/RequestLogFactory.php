<?php

namespace Bilfeldt\RequestLogger\Database\Factories;

use Bilfeldt\RequestLogger\Models\RequestLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestLogFactory extends Factory
{
    protected $model = RequestLog::class;

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'ip' => $this->faker->ipv4,
            'session' => null,
            'middleware' => $this->faker->words(3),
            'status' => $this->faker->randomElement([200, 201, 202, 204, 300, 301, 302, 303, 304, 400, 401, 402, 403, 404, 405, 406, 422, 429, 500, 501, 502, 503, 504]),
            'method' => $this->faker->randomElements(['GET', 'POST', 'PUT', 'PATCH', 'DELETE']),
            'route' => $this->faker->domainWord().'.'.$this->faker->randomElements(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']),
            'path' => null,
            'headers' => $this->getHeaders(),
            'payload' => $this->getPayload(),
            'response_headers' => $this->getHeaders(),
            'response_body' => $this->faker->sentence(),
            'duration' => $this->faker->numberBetween(100, 30000),
            'memory' => $this->faker->randomFloat(1, 1, 100),
        ];
    }

    protected function getPayload(): array
    {
        return [
            'type' => $this->faker->mimeType(),
            'extension' => $this->faker->fileExtension(),
        ];
    }

    protected function getHeaders(): array
    {
        return [
            'User-Agent' => $this->faker->userAgent(),
        ];
    }
}
