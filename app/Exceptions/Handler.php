<?php
namespace App\Exceptions;

use App\Helpers\Constant;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

// use HttpException;

/**
 * Class Handler
 * @package App\Exceptions
 */
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
     * @param  Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception) {
        
        parent::report($exception);
        // Log::error(
        //     get_class($exception) .
        //     '(code: ' . $exception->getCode() . '): ' .
        //     $exception->getMessage() . ' at ' .
        //     $exception->getFile() . ' on line ' .
        //     $exception->getLine()
        // );
        
    }
    
    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request $request
     * @param  Exception $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception) {

        if ($request->ajax() || $request->wantsJson()) {
            $status = Constant::BAD_REQUEST;
            $paths = explode('\\', get_class($exception));
            $response['message'] = $exception->getMessage();
            $response['file'] = $exception->getFile();
            $response['line'] = $exception->getLine();
            $eName = $paths[sizeof($paths) -1];
            switch ($eName) {
                case 'AuthorizationException':
                    $status = Constant::UNAUTHORIZED;
                    $response['message'] = __('messages.unauthorized');
                    break;
                // case 'InvalidPayloadException':
                //     $status = Constant::INTERNAL_SERVER_ERROR;
                //     $response['message'] = json_last_error_msg();
                //     break;
                case 'AuthenticationException':
                    $status = Constant::UNAUTHORIZED;
                    if ($request->method() == 'GET') {
                        if ($request->query('draw')) {
                            $response['returnUrl'] = $request->url() .
                                '?menuId=' . $request->query('menuId') .
                                '&tabId=' . $request->query('tabId');
                        } else {
                            $response['returnUrl'] = $request->fullUrl();
                        }
                    }
                    break;
                case 'InvalidArgumentException':
                    $status = Constant::INTERNAL_SERVER_ERROR;
                    $response['message'] = __('messages.invalid_argument');
                    break;
                case 'TokenMismatchException':
                    $status = Constant::TOKEN_MISMATCH;
                    $response['message'] = __('messages.token_mismatch');
                    break;
                case 'ErrorException':
                    $status = Constant::INTERNAL_SERVER_ERROR;
                    break;
                case 'HttpException':
                    /** @var HttpException $exception */
                    $status = $exception->getStatusCode();
                    break;
                case 'ValidationException':
                    /** @var ValidationException $exception */
                    $status = Constant::NOT_ACCEPTABLE;
                    $response['errors'] = $exception->errors();
                    break;
                case 'NotFoundHttpException':
                    $status = Constant::NOT_FOUND;
                    // $response['message'] = __('messages.not_found');
                    break;
                case 'MethodNotAllowedHttpException':
                    $status = Constant::METHOD_NOT_ALLOWED;
                    $response['message'] = __('messages.method_not_allowed');
                    break;
                default:
                    break;
            }
            # 如果是调试环境env('APP_DEBUG')，如果非调试环境
            $response['statusCode'] = $status;
            $response['exception'] = $eName;
            return response()->json($response, $status);
        }

        return parent::render($request, $exception);
        
    }
    
    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  Request $request
     * @param  AuthenticationException $exception
     * @return Response
     */
    protected function unauthenticated($request, AuthenticationException $exception) {
        
        return $request->expectsJson()
            ? response()->json(['error' => 'Unauthenticated.'], 401)
            : redirect()->guest(route('login'));
        
    }
    
}
