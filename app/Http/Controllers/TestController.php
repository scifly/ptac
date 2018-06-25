<?php
namespace App\Http\Controllers;

use App\Helpers\ModelTrait;
use App\Models\App;
use App\Models\Department;
use App\Models\Educator;
use App\Models\Score;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class TestController
 * @package App\Http\Controllers
 */
class TestController extends Controller {
    
    // use ModelTrait;
    
    protected $keyId = 'LTAIk1710IrzHBg4';
    protected $keySecret = 'xxO5XaXx3O7kB3YR14XSdFulw1x56k';
    protected $callerShowNumber = '02388373982';
    
    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function index() {
    
        
        dd(Educator::whereSchoolId(1)
            ->with('user')->get()->pluck('user.realname', 'id')
            ->toArray());
        $a = [
            0 => 'a',
            1 => 'b'
        ];
        
        dd(key($a));
        
        (new Score)->template();
        exit;
        // dd(round(microtime(true) * 1000));
        // dd(strtotime(date('Y-m-d'), now()));
        // dd(strtotime('2018-06-04') * 1000);
        // dd($records = Student::with('user:id,realname')
        //     ->where('class_id', 1)
        //     ->where('enabled', 1)
        //     ->get()->toArray()
        // );
        // $arrs = Group::whereIn('name', ['运营', '企业', '学校'])->get()->pluck('name', 'id')->toArray();
        // dd(($arrs));
        
        $action = 'queryCallDetailByCallId';
        ini_set("display_errors", "on"); // 显示错误提示，仅用于测试时排查问题
        // error_reporting(E_ALL); // 显示所有错误提示，仅用于测试时排查问题
        set_time_limit(0); // 防止脚本超时，仅用于测试使用，生产环境请按实际情况设置
        header("Content-Type: text/plain; charset=utf-8"); // 输出为utf-8的文本格式，仅用于测试
        switch ($action) {
            case 'clickToDial':
                // 验证点击拨号(ClickToDial)接口
                print_r($this->clickToDial());
                break;
            case 'cancelCall':
                // 验证取消呼叫(CancelCall)接口
                print_r($this->cancelCall());
                break;
            case 'ivrCall':
                // 验证交互式语音应答(IvrCall)接口
                print_r($this->ivrCall());
                break;
            case 'queryCallDetailByCallId':
                // 验证通过呼叫ID获取呼叫记录(QueryCallDetailByCallId)
                print_r($this->queryCallDetailByCallId());
                break;
            case 'singleCallByTts':
                // 验证文本转语音外呼(SingleCallByTts)接口
                print_r($this->singleCallByTts());
                break;
            case 'singleCallByVoice':
                // 验证语音文件外呼(SingleCallByVoice)接口
                print_r($this->singleCallByVoice());
                break;
            default:
                break;
        }
        
    }

    public function apiCall() {
    
        try {
            $client = new Client();
            $reponse = $client->post(
                'http://sandbox.ddd/ptac/public/api/login', [
                    'form_params' => [
                        'username' => 'haoyuhang',
                        'password' => '#ilikeit09',
                    ],
                ]
            );
            $token = json_decode($reponse->getBody()->getContents())->{'token'};
            $response = $client->post(
                'http://sandbox.ddd/ptac/public/api/upload_consumption', [
                    'headers'     => [
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'form_params' => [
                        'student_id' => 4,
                        'location'   => '食堂',
                        'machineid'  => 'm123456',
                        'ctype'      => 0,
                        'amount'     => 25.50,
                        'ctime'      => '2018-03-15 14:25:30',
                        'merchant'   => '青椒肉丝套饭',
                    ],
                ]
            );
            dd(json_decode($response->getBody(), true));
        } catch (ClientException $e) {
            echo $e->getResponse()->getStatusCode();
            echo $e->getResponse()->getBody()->getContents();
        }
        
    }
    
    public function listen() {
        
        return view('test.listen');
        
    }
    
    function getTraitMethodsRefs(ReflectionClass $class) {
        
        $traitMethods = call_user_func_array(
            'array_merge',
            array_map(
                function (ReflectionClass $ref) { return $ref->getMethods(); },
                $class->getTraits()
            )
        );
        $traitMethods = call_user_func_array(
            'array_merge',
            array_map(
                function (ReflectionMethod $method) { return [spl_object_hash($method) => $method->getName()]; },
                $traitMethods
            )
        );
        
        return $traitMethods;
        
    }
    
    function getClassMethodsRefs(ReflectionClass $class) {
        
        return call_user_func_array(
            'array_merge',
            array_map(
                function (ReflectionMethod $method) { return [spl_object_hash($method) => $method->getName()]; },
                $class->getMethods()
            )
        );
        
    }
    
    private function getLevel($id, &$level) {
        
        /** @var Department $parent */
        $parent = Department::find($id)->parent;
        if ($parent) {
            $level += 1;
            $this->getLevel($parent->id, $level);
        }
        
        return $level;
        
    }
    
    private function clickToDial() {
    
        $params = [];
    
        // *** 需用户填写部分 ***
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    
        // fixme 必填: 主叫显号, 可在语音控制台中找到所购买的显号
        $params["CallerShowNumber"] = $this->callerShowNumber;
    
        // fixme 必填: 主叫号码
        $params["CallerNumber"] = "1800000000";
    
        // fixme 必填: 被叫显号, 可在语音控制台中找到所购买的显号
        $params["CalledShowNumber"] = "4001112222";
    
        // fixme 必填: 被叫号码
        $params["CalledNumber"] = "13700000000";
    
        // fixme 可选: 是否录音
        $params["RecordFlag"] = true;
    
        // fixme 可选: 是否开启实时ASR功能
        $params["AsrFlag"] = true;
    
        // fixme 可选: ASR模型ID
        $params["AsrModelId"] = '2070aca1eff146f9a7bc826f1c3d4d33';
    
        // fixme 可选: 预留给调用方使用的ID, 最终会通过在回执消息中将此ID带回给调用方（15个字符及以内）
        $params["OutId"] = "yourOutId";
    
        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();
    
        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $this->keyId,
            $this->keySecret,
            "dyvmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "ClickToDial",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );
    
        return $content;
    }
    
    private function cancelCall() {
    
        $params = array();
    
        // *** 需用户填写部分 ***
    
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    
        // fixme 必填: 从上次呼叫调用的返回值中获取的CallId
        $params["CallId"] = "113853585007^100675005007";
    
        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();
    
        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $this->keyId,
            $this->keySecret,
            "dyvmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "CancelCall",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );
    
        return $content;
    }
    
    private function ivrCall() {
    
        $params = array();
    
        // *** 需用户填写部分 ***
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    
        // fixme 必填: 被叫显号
        $params["CalledShowNumber"] = "4001112222";
    
        // fixme 必填: 被叫显号
        $params["CalledNumber"] = "13700000000";
    
        // fixme 必填: 呼叫开始时播放的提示音-语音文件Code名称或者Tts模板Code
        $params["StartCode"] = "TTS_10001";
    
        // fixme 可选: Tts模板中的变量替换JSON,假如Tts模板中存在变量，则此处必填
        $params["StartTtsParams"] = array("AckNum" => "123456");
    
        // fixme 必填: 按键与语音文件ID或tts模板的映射关系
        $menuKeyMaps = [
            [ // 按下1键, 播放语音
                "Key" => "1",
                "Code" => "9a9d7222-670f-40b0-a3af.wav"
            ],
            [ // 按下2键, 播放语音
                "Key" => "2",
                "Code" => "44e3e577-3d3a-418f-932c.wav"
            ],
            [ // 按下3键, 播放TTS语音
                "Key" => "3",
                "Code" => "TTS_71390000",
                "TtsParams" => ["product" =>"aliyun", "code" =>"123"]
            ],
        ];
    
        // fixme 可选: 重复播放次数
        $params["PlayTimes"] = 3;
    
        // fixme 可选: 等待用户按键超时时间，单位毫秒
        $params["Timeout"] = 3000;
    
        // fixme 可选: 播放结束时播放的结束提示音,支持语音文件和Tts模板2种方式,但是类型需要与StartCode一致，即前者为Tts类型的，后者也需要是Tts类型的
        $params["ByeCode"] = "TTS_71400007";
    
        // fixme 可选: Tts模板变量替换JSON,当ByeCode为Tts时且Tts模板中带变量的情况下此参数必填
        $params["ByeTtsParams"] = ["product" => "aliyun", "code" => "123"];
    
        // fixme 可选: 预留给调用方使用的ID, 最终会通过在回执消息中将此ID带回给调用方
        $params["OutId"] = "yourOutId";
    
        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    
        if(!empty($params["StartTtsParams"]) && is_array($params["StartTtsParams"])) {
            $params["StartTtsParams"] = json_encode($params["StartTtsParams"], JSON_UNESCAPED_UNICODE);
        }
    
        if(!empty($params["ByeTtsParams"]) && is_array($params["ByeTtsParams"])) {
            $params["ByeTtsParams"] = json_encode($params["ByeTtsParams"], JSON_UNESCAPED_UNICODE);
        }
    
        $i = 0;
        foreach($menuKeyMaps as $menuKeyMap) {
            ++$i;
            $params["MenuKeyMap." . $i . ".Key"] = $menuKeyMap["Key"];
            $params["MenuKeyMap." . $i . ".Code"] = $menuKeyMap["Code"];
            if(!empty($menuKeyMap["TtsParams"]) && is_array($menuKeyMap["TtsParams"])) {
                $params["MenuKeyMap." . $i . ".TtsParams"] = json_encode($menuKeyMap["TtsParams"], JSON_UNESCAPED_UNICODE);
            }
        }
    
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();
        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $this->keyId,
            $this->keySecret,
            "dyvmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "IvrCall",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );
    
        return $content;
    }
    
    private function queryCallDetailByCallId() {
    
        $params = [];
    
        // *** 需用户填写部分 ***
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    
        // fixme 必填: 从上次呼叫调用的返回值中获取的CallId
        $params["CallId"] = "114279786362^101089366362";
    
        // fixme 必填: Unix时间戳（毫秒），会查询这个时间点对应那一天的记录
        $params["QueryDate"] = "1528041600000";
    
        // fixme 必填: 语音通知为:11000000300006, 语音验证码为:11010000138001, IVR为:11000000300005, 点击拨号为:11000000300004, SIP为:11000000300009
        $params["ProdId"] = "11010000138001";
    
        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();
    
        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $this->keyId,
            $this->keySecret,
            "dyvmsapi.aliyuncs.com",
            array_merge($params, [
                "RegionId" => "cn-hangzhou",
                "Action" => "QueryCallDetailByCallId",
                "Version" => "2017-05-25",
            ])
        // fixme 选填: 启用https
        // ,true
        );
    
        return $content;
    }
    
    private function singleCallByTts() {
        
        $params = array ();
        
        // *** 需用户填写部分 ***
        
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息

        // fixme 必填: 被叫显号
        $params["CalledShowNumber"] = $this->callerShowNumber;
        
        // fixme 必填: 被叫显号
        $params["CalledNumber"] = "13700000000";
        
        // fixme 必填: Tts模板Code
        $params["TtsCode"] = "TTS_129743194";
        
        // fixme 选填: Tts模板中的变量替换JSON,假如Tts模板中存在变量，则此处必填
        $params["TtsParam"] = ["var1" => "123456"];
        
        // fixme 选填: 音量
        $params["Volume"] = 100;
        
        // fixme 选填: 播放次数
        $params["PlayTimes"] = 3;
        
        // fixme 选填: 音量, 取值范围 0~200
        $params["Volume"] = 100;
        
        // fixme 选填: 预留给调用方使用的ID, 最终会通过在回执消息中将此ID带回给调用方
        $params["OutId"] = "yourOutId";
        
        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        
        if(!empty($params["TtsParam"]) && is_array($params["TtsParam"])) {
            $params["TtsParam"] = json_encode($params["TtsParam"], JSON_UNESCAPED_UNICODE);
        }
        
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();
        
        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $this->keyId,
            $this->keySecret,
            "dyvmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SingleCallByTts",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );
        
        return $content;
    }
    
    private function singleCallByVoice() {
    
        $params = array ();
    
        // *** 需用户填写部分 ***
    
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    
        // fixme 必填: 被叫显号
        $params["CalledShowNumber"] = $this->callerShowNumber;
    
        // fixme 必填: 被叫显号
        $params["CalledNumber"] = "13700000000";
    
        // fixme 必填: 语音文件Code
        $params["VoiceCode"] = "c2e99ebc-2d4c-4e78-8d2a-afbb06cf6216.wav";
    
        // fixme 选填: 音量
        $params["Volume"] = 100;
    
        // fixme 选填: 播放次数
        $params["PlayTimes"] = 3;
    
        // fixme 选填: 外呼流水号
        // $params["OutId"] = "yourOutId";
    
        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();
    
        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $this->keyId,
            $this->keySecret,
            "dyvmsapi.aliyuncs.com",
            array_merge($params, [
                "RegionId" => "cn-hangzhou",
                "Action" => "SingleCallByVoice",
                "Version" => "2017-05-25",
            ])
        // fixme 选填: 启用https
        // ,true
        );
    
        return $content;
    }
    
}
