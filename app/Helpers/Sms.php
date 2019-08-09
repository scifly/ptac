<?php
namespace App\Helpers;

use Throwable;

/**
 * Class Sms
 * @package App\Helpers
 */
class Sms {
    
    use ApiTrait;
    
    const BASE_URI = 'http://sdk2.028lk.com:9880/sdk2/';
    const CREDENTIAL = ['CorpID' => '12345678', 'Pwd' => '12345678'];
    const APIS = [
        'UpdReg'     => ['CorpName', 'LinkMan', 'Tel', 'Mobile', 'Email', 'Memo'],
        'UpdPwd'     => ['NewPwd'],
        'BatchSend2' => ['Mobile', 'Content', 'Cell', 'SendTime'],
        'Selsum'     => [],
        'Get'        => [],
    ];
    
    /**
     * 调用接口
     *
     * @param $api
     * @param null $values
     * @return bool|string
     * @throws Throwable
     */
    function invoke($api, $values = null) {
        
        $url = join([
            self::BASE_URI,
            $api . '.aspx?',
            http_build_query(
                array_merge(
                    self::CREDENTIAL,
                    array_combine(
                        self::APIS[$api], $values ?? []
                    )
                )
            )
        ]);
        
        return $this->curlGet($url);
        
    }
    
}