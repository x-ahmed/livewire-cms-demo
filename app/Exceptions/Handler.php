<?php

namespace App\Exceptions;

use Throwable;
use App\Models\Page;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Throwable $e, $request) {
            if ($e instanceof NotFoundHttpException) {
                $default404Page = Page::whereIsDefaultNotFound(true)->first()->slug;
                return redirect(route('front-page', $default404Page));
            }

            report($e);
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
