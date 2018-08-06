<?php
namespace App\Http\Controllers;

use App\Models\WechatSms;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * 微信企业应用
 *
 * Class AppController
 * @package App\Http\Controllers
 */
class WechatSmsController extends Controller {
    
    protected $wechatSms;
    
    /**
     * AppController constructor.
     * @param WechatSms $wechatSms
     */
    function __construct(WechatSms $wechatSms) {
        
        $this->wechatSms = $wechatSms;
        
    }
    
    /**
     * 显示微信消息详情
     *
     * @param $urlcode
     * @return Factory|View
     */
    function show($urlcode) {
        
        return $this->wechatSms->show($urlcode);
        
    }
    
    
}
