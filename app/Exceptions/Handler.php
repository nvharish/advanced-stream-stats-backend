<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof HttpException) {
            $status_code = $exception->getStatusCode();
        } elseif ($exception instanceof ValidationException) {
            $status_code = $exception->getResponse()->getStatusCode();
            $errors = $exception->errors();
        } elseif ($exception instanceof DomainException || $exception instanceof SignatureInvalidException) {
            $status_code = Response::HTTP_UNAUTHORIZED;
            $errors = $exception->getMessage();
        } elseif ($exception instanceof QueryException) {
            $status_code = Response::HTTP_BAD_REQUEST;
        } else {
            $status_code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $response = array(
            'status' => 'ERROR',
            'message' => Response::$statusTexts[$status_code] ?? 'Something went wrong',
        );
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $status_code);
    }
}
