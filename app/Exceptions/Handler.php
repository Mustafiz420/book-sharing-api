<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Services\ResponseService;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // Let explicitly built JSON responses pass through
        if ($e instanceof HttpResponseException) {
            return parent::render($request, $e);
        }

        $response = app(ResponseService::class);

        // Validation errors
        if ($e instanceof ValidationException) {
            return $response->validation($e->errors());
        }

        // Authentication / Authorization
        if ($e instanceof AuthenticationException) {
            return $response->error('Unauthenticated', 401);
        }
        if ($e instanceof AuthorizationException) {
            return $response->error('Forbidden', 403);
        }

        // Not found / method errors
        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return $response->error('Not Found', 404);
        }
        if ($e instanceof MethodNotAllowedHttpException) {
            return $response->error('Method Not Allowed', 405);
        }

        // Database
        if ($e instanceof QueryException) {
            return $response->error('Database error', 500);
        }

        // Generic HTTP exceptions
        if ($e instanceof HttpExceptionInterface) {
            return $response->error($e->getMessage() ?: 'HTTP Error', $e->getStatusCode());
        }

        // Fallback
        if (config('app.debug')) {
            return parent::render($request, $e);
        }

        return $response->error('Server Error', 500);
    }
}
