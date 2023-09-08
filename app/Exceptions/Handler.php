<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
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

    /**
     * Render the exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Content-Type, Content-Disposition, Cache-Control, Authorization, X-Requested-With, X-CSRF-TOKEN, Access-Control-Expose-Headers, X-Timezone, X-Localization, Access-Control-Request-Headers, Access-Control-Request-Method'
        ];

        $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        $msg = '';

        if ($e instanceof HttpResponseException) {
            if (config('app.debug')) return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
            ], $code, $headers);
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $code = Response::HTTP_METHOD_NOT_ALLOWED;
            $msg = $e->getMessage();
        } elseif ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
            $code = Response::HTTP_NOT_FOUND;
            $msg = $e->getMessage();
        } elseif ($e instanceof AuthorizationException) {
            $code = Response::HTTP_FORBIDDEN;
            $msg = $e->getMessage();
        } elseif ($e instanceof AuthenticationException) {
            $code = Response::HTTP_UNAUTHORIZED;
            $msg = $e->getMessage();
        } elseif ($e instanceof ValidationException) {
            $errorBag = [];
            $message  = $e->getMessage();
            foreach ($e->errors() as $key => $value) $errorBag[] = ['attribute' => $key, 'text' => $value[0]];
            if (!empty($errorBag)) {
                $message = $errorBag[0]['text'];
            }
            return response()->json([
                'status'  => false,
                'message' => $message,
                'data'    => (object)[],
                'meta'    => (object)[],
                'error'   => $errorBag
            ], Response::HTTP_OK);
        } else {
            if (config('app.debug')) return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
            ], $code, $headers);
        }

        return response($msg, $code);
    }
}
