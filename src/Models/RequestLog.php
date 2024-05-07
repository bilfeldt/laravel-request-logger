<?php

namespace Bilfeldt\RequestLogger\Models;

use Bilfeldt\RequestLogger\Contracts\RequestLoggerInterface;
use Bilfeldt\RequestLogger\Database\Factories\RequestLogFactory;
use Bilfeldt\RequestLogger\RequestLoggerFacade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RequestLog extends Model implements RequestLoggerInterface
{
    use HasFactory;
    use MassPrunable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ip',
        'session',
        'middleware',
        'status',
        'method',
        'route',
        'path',
        'headers',
        'payload',
        'response_headers',
        'response_body',
        'duration',
        'memory',
    ];

    protected $casts = [
        'middleware' => 'json',
        'headers' => 'json',
        'payload' => 'json',
        'response_headers' => 'json',
        'response_body' => 'json',
    ];

    //======================================================================
    // ACCESSORS
    //======================================================================

    //======================================================================
    // MUTATORS
    //======================================================================

    //======================================================================
    // SCOPES
    //======================================================================

    //======================================================================
    // RELATIONS
    //======================================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('request-logger.user_model') ?? config('auth.providers.users.model'));
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo('App\\Models\\Team');
    }

    //======================================================================
    // METHODS
    //======================================================================

    /**
     * Get the prunable model query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function prunable()
    {
        return static::where('created_at', '<=', Date::now()->subDays(config('request-logger.drivers.model.prune')));
    }

    /** @inerhitDoc */
    public function log(Request $request, $response, ?int $duration = null, ?int $memory = null): void
    {
        $model = new static();

        $model->uuid = $request->getUniqueId();
        $model->correlation_id = $this->truncateToLength($request->getCorrelationId());
        $model->client_request_id = $this->truncateToLength($request->getClientRequestId());
        $model->ip = $request->ip();
        $model->session = $request->hasSession() ? $request->session()->getId() : null;
        $model->middleware = array_values(optional($request->route())->gatherMiddleware() ?? []);
        $model->method = $request->getMethod();
        $model->route = $this->truncateToLength(optional($request->route())->getName() ?? optional($request->route())->uri()); // Note that $request->route()->uri() does not replace the placeholders while $request->getRequestUri() replaces the placeholders
        $model->path = $this->truncateToLength($request->path());
        $model->status = $response->getStatusCode();
        $model->headers = $this->getFiltered($request->headers->all()) ?: null;
        $model->payload = $this->getFiltered($request->input()) ?: null;
        $model->response_headers = $this->getFiltered($response->headers->all()) ?: null;
        $model->response_body = $this->getLoggableResponseContent($response);
        $model->duration = $duration;
        $model->memory = round($memory / 1024 / 1024, 2); // [MB]

        if ($user = $request->user()) {
            $model->user()->associate($user);
        }

        if ($team = $this->getRequestTeam($request)) {
            $model->team()->associate($team);
        }

        $model->save();
    }

    protected function getRequestTeam(Request $request): ?Model
    {
        if ($request->route('team') instanceof Model) {
            return $request->route('team');
        }

        if ($user = $request->user()) {
            return method_exists($user, 'currentTeam') ? $user->currentTeam : null;
        }

        return null;
    }

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

    protected static function newFactory()
    {
        return RequestLogFactory::new();
    }
}
