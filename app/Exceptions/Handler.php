<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler {
    
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        ValidationException::class,
    ];
    
    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception) {
        // parent::report($exception);
        Log::error(
            get_class($exception) .
            '(code: ' . $exception->getCode() . '): ' .
            $exception->getMessage() . ' at ' .
            $exception->getFile() . ' on line ' .
            $exception->getLine()
        );
    }
    
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception) {

        if ($request->ajax() || $request->wantsJson()) {
            $response = ['message' => $exception->getMessage()];
            if (env('APP_DEBUG')) {
               $response['exception'] = get_class($exception);
               $response['file'] = $exception->getFile();
               $response['line'] = $exception->getLine();
            }
            $status = 400;
            if ($this->isHttpException($exception)) {
               $status = $exception->getCode();
            }
            return response()->json($response, $status);
        }
        return parent::render($request, $exception);
        
    }
    
    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception) {
        
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        
        return redirect()->guest(route('login'));
        
    }
    
}
