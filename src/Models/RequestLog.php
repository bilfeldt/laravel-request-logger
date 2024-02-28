<?php

namespace Bilfeldt\RequestLogger\Models;

use Bilfeldt\RequestLogger\Contracts\RequestLoggerInterface;
use Bilfeldt\RequestLogger\Database\Factories\RequestLogFactory;
use Bilfeldt\RequestLogger\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class RequestLog extends Model implements RequestLoggerInterface
{
    use HasFactory;
    use MassPrunable;
    use Loggable;

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
        return $this->belongsTo(config('auth.providers.users.model'));
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

    protected static function newFactory()
    {
        return RequestLogFactory::new();
    }
}
