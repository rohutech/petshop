<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Traits\API\RestTrait;

class Handler extends ExceptionHandler
{
    use RestTrait;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($request->wantsJson() && $this->isApiCall($request)) {   //add Accept: application/json in request
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * handle any API exception in a custom way
     * and prepare any custom message if required
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleApiException($request, $exception)
    {
        return $this->customApiResponse($exception);
    }

    /**
     * prepare a custom API response expected in the front-end
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    private function customApiResponse($exception)
    {
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $errorMessage = "Failed Validation";
        $response = [
            'success' => 0,
            'data'    => [],
            'error'   => $errorMessage,
            'errors'  => [$exception->getMessage()],
            'extra'   => []
        ];

        if (config('app.debug')) {
            $response['extra']['trace'] = $exception->getTrace();
            $response['extra']['code']  = $exception->getCode();
        }

        return response()->json($response, $statusCode);
    }
}
