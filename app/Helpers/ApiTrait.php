<?php
namespace App\Helpers;

use Exception;
use Throwable;

/**
 * Trait ApiTrait
 * @package App\Helpers
 */
trait ApiTrait {
    
    /**
     * 发送GET请求
     *
     * @param string $url
     * @return bool|string
     * @throws Throwable
     */
    function curlGet(string $url) {
        
        try {
            curl_setopt_array($ch = curl_init(), [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_0,
            ]);
            throw_if(
                !$result = curl_exec($ch),
                new Exception(curl_error($ch), curl_errno($ch))
            );
            curl_close($ch);
        } catch (Exception $e) {
            throw $e;
        }
        
        return $result;
        
    }
    
    /**
     * 发送POST请求
     *
     * @param string|array $data
     * @param $url
     * @return bool|string|null
     * @throws Throwable
     */
    function curlPost($data, $url) {
        
        try {
            curl_setopt_array($ch = curl_init(), [
                CURLOPT_URL            => $url,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)',
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_AUTOREFERER    => 1,
                CURLOPT_POSTFIELDS     => $data,
                CURLOPT_RETURNTRANSFER => true,
            ]);
            throw_if(
                !$result = curl_exec($ch),
                new Exception(curl_error($ch), curl_errno($ch))
            );
            curl_close($ch);
        } catch (Exception $e) {
            throw $e;
        }
        
        return $result;
        
    }
    
}