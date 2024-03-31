<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if($e instanceof NotFoundHttpException) {
                    Log::info('From renderable method: '.$e->getMessage());
                    return response()->json([
                        'message' => $e->getMessage(),
                        'status' => false
                    ], 404);
                }

                if($e instanceof HttpException) {
                    Log::info('From renderable method: '.$e->getMessage());
                    return response()->json([
                        'message' => $e->getMessage(),
                        'status' => false
                    ], 400);
                }
               
            }
        });
    }
}