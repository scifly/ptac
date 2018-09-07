<?php
namespace App\Helpers\Wechat;

use Exception;

/**
 * 微信支付API异常类
 *
 * @author widyhu
 */
class WxPayException extends Exception {
    
    /**
     * @return mixed
     */
    public function errorMessage() {
        
        return $this->getMessage();
        
    }
    
}
