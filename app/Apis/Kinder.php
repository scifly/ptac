<?php
namespace App\Apis;

use App\Events\JobResponse;
use App\Helpers\HttpStatusCode;
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
    // const URL = 'http://eccard.eicp.net:8078/Dispatch.aspx';
    const URL = 'http://192.168.10.117:8084/Dispatch.aspx';
    const METHOD_CODE = [
        '10' => '新增部门',
        '11' => '编辑部门',
        '12' => '删除部门',
        '13' => '新增人员',
        '14' => '编辑人员',
        '15' => '删除人员',
        '16' => '充值',
    ];
    
    protected $type, $action, $data, $response;
    
    /**
     * Kinder constructor.
     *
     * @param $type
     * @param $action
     * @param $data
     * @param $response
     */
    function __construct($type, $action, $data, $response) {
    
        $this->type = $type;
        $this->action = $action;
        $this->data = $data;
        $this->response = $response;
        
    }
    
    /**
     * 同步通讯录
     *
     * @throws Exception
     */
    function sync() {
    
        $name = '';
        switch ($this->action) {
            case 'create':
                $name = '新增';
                break;
            case 'update':
                $name = '编辑';
                break;
            case 'delete':
                $name = '删除';
                break;
            default:
                break;
        }
        $this->response['title'] = $name . '卡德' . $this->type;
        $this->response['message'] = __('messages.synced') . '卡德';
        $hasError = false;
        $result = json_decode(
            $this->call($name . $this->type, $this->data), true
        );
        if (!$result) {
            $hasError = true;
        } else {
            if (isset($result['code'])) {
                if ($result['code']) {
                    $hasError = true;
                }
            } else {
                if ($result['result']) {
                    $hasError = true;
                }
            }
        }
        if ($hasError) {
            $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $this->response['message'] = '同步失败';
        }
        if ($this->response['userId']) {
            event(new JobResponse($this->response));
        }

        return $this->response;
        
    }
    
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
            $response = (new Client)->post(
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
            
            return $response->getBody()->getContents();
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
            case '10':      # 新增部门
            case '11':      # 编辑部门
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
            case '12':      # 删除部门
                $params = ['did' => $data['id']];
                break;
            case '13':      # 新增人员
            case '14':      # 编辑人员
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
            case '15':      # 删除人员
                $user = User::whereUserid($data['userid'])->first();
                $params = ['cid' => $user->id + 10000];
                break;
            case '16':      # 充值
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