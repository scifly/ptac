<?php
namespace App\Apis;

use App\Helpers\Broadcaster;
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
    const URL = 'http://172.16.0.251:8084/Dispatch.aspx';
    const METHOD_CODE = [
        '10' => '新增部门',
        '11' => '编辑部门',
        '12' => '删除部门',
        '13' => '新增人员',
        '14' => '编辑人员',
        '15' => '删除人员',
        '16' => '充值',
    ];
    const ACTION_NAME = [
        'create' => '新增',
        'update' => '编辑',
        'delete' => '删除'
    ];
    
    protected $type, $action, $data, $response, $broadcaster;
    
    /**
     * Kinder constructor.
     *
     * @param $type
     * @param $action
     * @param $data
     * @param $response
     * @throws \Pusher\PusherException
     */
    function __construct($type, $action, $data, $response) {
    
        $this->type = $type;
        $this->action = $action;
        $this->data = $data;
        $this->response = $response;
        $this->broadcaster = new Broadcaster();
        
    }
    
    /**
     * 同步通讯录
     *
     * @throws Exception
     */
    function sync() {
    
        $name = self::ACTION_NAME[$this->action];
        $this->response['title'] = $name . '卡德' . $this->type;
        $this->response['message'] = __('messages.synced') . '卡德';
        $this->response['statusCode'] = HttpStatusCode::OK;
        $result = json_decode(
            $this->call($name . $this->type, $this->data), true
        );
        if ($result ? ($result['code'] ? true : false ) : true) {
            $this->response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $this->response['message'] = '同步失败';
        }
        if ($this->response['userId']) {
            $this->broadcaster->broadcast($this->response);
        }

        return $result;
        
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
                $id = $data['id'];
                $parentId = $data['parentid'];
                $department = Department::find($id);
                $dtName = $department->departmentType->name;
                $params = [
                    'did'         => $id + 10000,
                    'dname'       => $data['name'],
                    'dfather'     => $parentId == 33 ? 10000 : $parentId + 10000,
                    'dexpiration' => '2020-09-01 23:59:59',
                    'dnumber'     => $department->users->count(),
                    'dtel'        => in_array($dtName, ['年级', '班级']) ? $dtName : 'n/a',
                ];
                break;
            case '12':      # 删除部门
                $params = ['did' => $data['id'] + 10000];
                break;
            case '13':      # 新增人员
            case '14':      # 编辑人员
                $id = $data['id'];
                $user = User::find($id);
                $departmentId = head($user->depts($id)->pluck('id')->toArray());
                $did = $departmentId + (!$user->custodian ? 10000 : 50000);
                $cnumber = $data['username'];
                if ($user->student) {
                    $cnumber = $user->student->student_number;
                } elseif ($user->custodian) {
                    $cnumber = head($user->custodian->students->pluck('student_number')->toArray()) . $id;
                }
                $params = [
                    'cid'     => $id + 10000,
                    'cnumber' => $cnumber,
                    'cname'   => $data['name'],
                    'did'     => $did,
                    'sex'     => $data['gender'] ? 0 : 1,
                    'cardid'  => $cnumber,
                    'tel'     => head($user->mobiles->pluck('mobile')->toArray()),
                    'post'    => $user->group_id,
                    'status'  => $user->enabled,
                    'address' => 'n/a',
                    'ishome'  => $user->role($id) == '学生' ? $data['remark'] : 0,
                    'remark'  => $data['remark'],
                    'bank'    => $user->custodian ? 'custodian' : 'n/a',
                ];
                break;
            case '15':      # 删除人员
                $params = ['cid' => $data['id'] + 10000];
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