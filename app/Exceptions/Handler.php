<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Inertia\Inertia;
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

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        $this->renderable(function (\Throwable $e, $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return response()->json([
                    'errorType' => true,
                    'message'   => 'Unauthenticated. Please login again.',
                    'data'      => null,
                    'errors'    => null,
                ], 401);
            }
            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return response()->json([
                    'errorType' => true,
                    'message'   => 'You are not authorized to perform this action.',
                    'data'      => null,
                    'errors'    => null,
                ], 403);
            }
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'errorType' => true,
                    'message'   => 'Validation failed',
                    'data'      => null,
                    'errors'    => $e->errors(),
                ], 422);
            }
    
            // Model not found (e.g. findOrFail with non-existent ID)
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json([
                    'errorType' => true,
                    'message'   => 'Resource not found.',
                    'data'      => null,
                    'errors'    => null,
                ], 404);
            }
    
            // Route not found
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return response()->json([
                    'errorType' => true,
                    'message'   => 'Endpoint not found.',
                    'data'      => null,
                    'errors'    => null,
                ], 404);
            }
    
            // Wrong HTTP method
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                return response()->json([
                    'errorType' => true,
                    'message'   => 'Method not allowed for this endpoint.',
                    'data'      => null,
                    'errors'    => null,
                ], 405);
            }
    
            // Rate limit exceeded
            if ($e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
                return response()->json([
                    'errorType' => true,
                    'message'   => 'Too many requests. Please slow down and try again shortly.',
                    'data'      => null,
                    'errors'    => null,
                ], 429);
            }
    
            // Fallback — generic server error
            // In production, never leak internal error details
            if (config('app.debug')) {
                return response()->json([
                    'errorType' => true,
                    'message'   => $e->getMessage(),
                    'data'      => null,
                    'errors'    => [
                        'exception' => get_class($e),
                        'file'      => $e->getFile(),
                        'line'      => $e->getLine(),
                    ],
                ], 500);
            }
    
            return response()->json([
                'errorType' => true,
                'message'   => 'Server error. Please try again later.',
                'data'      => null,
                'errors'    => null,
            ], 500);
        });
    }
    
    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        if (!$request->expectsJson() && in_array($response->status(), [403, 404, 419, 429, 500, 503])) {
            return Inertia::render('Errors/Show', [
                'status' => $response->status(),
            ])
                ->toResponse($request)
                ->setStatusCode($response->status());
        }

        return $response;
    }

}
