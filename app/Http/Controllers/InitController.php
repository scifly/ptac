<?php
namespace App\Http\Controllers;

use App\Helpers\Configure;
use App\Helpers\HttpStatusCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 系统初始化
 *
 * Class InitController
 * @package App\Http\Controllers
 */
class InitController extends Controller {
    
    protected $config;
    
    /**
     * AlertTypeController constructor.
     * @param Configure $config
     */
    function __construct(Configure $config) {
        
        $this->middleware(['auth', 'checkrole']);
        // abort_if(
        //     Auth::user()->role() != '运营',
        //     HttpStatusCode::UNAUTHORIZED,
        //     __('messages.unauthorized')
        // );
        $this->config = $config;
        
    }
    
    /**
     * 系统参数
     *
     * @throws Throwable
     */
    public function index() {
        
        return Request::method() == 'POST'
            ? (Request::input('action') == 'list'
                ? $this->config->html()
                : $this->result(
                    $this->config->init()
                )
            )
            : $this->output();
        
    }
    
}
