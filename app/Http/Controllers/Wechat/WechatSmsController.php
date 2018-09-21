<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\WechatSms;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * 微信消息短信
 *
 * Class WechatSmsController
 * @package App\Http\Controllers\Wechat
 */
class WechatSmsController extends Controller {
    
    protected $wechatSms;
    
    /**
     * WechatSmsController constructor.
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
