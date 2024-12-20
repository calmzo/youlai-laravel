<?php

namespace App\Exceptions;

use App\Exceptions\Token\ForbiddenException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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

    public function render($request, Throwable $e)
    {
        if ($e instanceof BusinessException) {
            return response()->json([
                'code' => $e->getStringCode(),
                'msg'  => $e->getMessage()
            ], 403);
        } else if ($e instanceof ForbiddenException) {
            return response()->json([
                'code' => $e->getStringCode(),
                'msg'  => $e->getMessage()
            ], 403);
        } else{
            return response()->json([
                'code' => $e->getCode(),
                'msg'  => $e->getMessage()
            ]);
        }
        return parent::render($request, $e); // TODO: Change the autogenerated stub
    }
}
