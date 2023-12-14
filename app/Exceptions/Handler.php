<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $this->renderable(function (\Exception $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Record not found.'
                    ], 404);
                } elseif ($e instanceof \Illuminate\Database\QueryException) {
                    // Handle database-related errors
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Database error.'
                    ], 500);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => $e->getMessage()
                    ], 400);
                }
            }
        });
    }
}
