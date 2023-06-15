<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;

use Throwable;

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

    public function render($request, Throwable $exception)
    {
        if ($request->isJson()) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->json(
                    [
                        'message' => 'External API call failed',
                        'error' => 'Entry for ' . str_replace('App', '', $exception->getModel()) . ' not found'
                    ],
                    404
                );
            } else if ($exception instanceof AuthenticationException) {
                return response()->json(['error' => 'Unauthenticated', 'message' => 'Unauthenticated'], 401);
            } else if ($exception instanceof ValidationException) {
                return response()->json(
                    [
                        'message' => 'Validation exception.',
                        'error' => $exception->getMessage()
                    ],
                    400
                );
            } else if ($exception instanceof UnauthorizedException) {
                return response()->json(['error' => 'Unauthenticated', 'message' => 'Unauthenticated'], 403);
            } else {
                return response()->json(
                    [
                        'message' => 'External API call failed',
                        'error' => $exception->getMessage()
                    ],
                    500
                );
            }
        }
        return parent::render($request, $exception);
    }
}
