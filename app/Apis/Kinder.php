<?php
namespace App\Apis;

use App\Models\Department;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;

/**
 * Class Kinder - 卡德接口
 * @package App\Apis
 */
class Kinder {
    
    const APP_ID = '5100000025';
    const APP_SECRET = 'B4C6F3A34F5936CEBA92C008F12B0396';
    const URL = 'http://eccard.eicp.net:8078/Dispatch.aspx';
    const METHOD_CODE = [
        '10' => '新增部门',
        '11' => '编辑部门',
        '12' => '删除部门',
        '13' => '新增人员',
        '14' => '编辑人员',
        '15' => '删除人员',
        '16' => '充值',
    ];
    
    /**
     * 接口调用
     * @param $name
     * @param array $data
     * @return string
     * @throws Exception
     */
    function call($name, array $data) {
        
        $method = array_search($name, self::METHOD_CODE);
        $nonce = mt_rand(0, 1000);
        #JSON数据串BASE64编码
        $params = base64_encode($this->params($method, $data));
        #MD5(16位)运算获得签名hash验证数据，Base64编码
        $hash = urlencode(
            base64_encode(
                strtoupper(
                    substr(
                        md5(self::APP_ID . '|' . self::APP_SECRET . '|' . $params . '|' . $nonce),
                        8, 16
                    )
                )
            )
        );
        try {
            $reponse = (new Client)->post(
                self::URL, [
                    'form_params' => [
                        'appid'  => self::APP_ID,
                        'hash'   => $hash,
                        'method' => $method,
                        'data'   => $params,
                        'nonce'  => $nonce,
                    ],
                ]
            );
            
            return $reponse->getBody()->getContents();
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 返回指定接口对应的请求参数字符串
     *
     * @param $method
     * @param array $data
     * @return string
     */
    private function params($method, array $data) {
        
        $params = '';
        switch ($method) {
            case '10':
            case '11':
                $params = [
                    'dname'       => $data['name'],
                    'dfather'     => $data['parentid'],
                    'dexpiration' => '2020-09-01 23:59:59',
                    'dnumber'     => Department::find($data['id'])->users->count(),
                    'dtel'        => 'n/a',
                ];
                if ($method == '11') {
                    $params = array_merge($params, ['did' => $data['id'] + 10000]);
                }
                break;
            case '12':
                $params = ['did' => $data['id']];
                break;
            case '13':
            case '14':
                $user = User::whereUserid($data['userid'])->first();
                $params = [
                    'cnumber' => $data['userid'],
                    'cname'   => $data['name'],
                    'did'     => head($user->departments->pluck('id')->toArray()),
                    'sex'     => $data['gender'],
                    'cardid'  => 'n/a',
                    'tel'     => $user->telephone,
                    'post'    => $user->group_id,
                    'status'  => $user->enabled,
                    'address' => 'n/a',
                    'remark'  => 'n/a',
                    'bank'    => 'n/a',
                ];
                if ($method == '14') {
                    $params = array_merge($params, ['cid' => $user->id + 10000]);
                }
                break;
            case '15':
                $user = User::whereUserid($data['userid'])->first();
                $params = ['cid' => $user->id + 10000];
                break;
            case '16':
                $params = [
                    'qcls'     => '',
                    'cid'      => '',
                    'category' => '',
                    'amount'   => '',
                    'balance'  => '',
                    'type'     => '',
                    'qdate'    => '',
                ];
                break;
            default:
                break;
            
        }
        
        return json_encode($params);
        
    }
    
}