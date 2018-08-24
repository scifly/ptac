<?php
namespace App\Http\Controllers;

use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Http\Requests\SchoolRequest;
use App\Models\Corp;
use App\Models\Department;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Facades\Request;
use Pusher\Pusher;
use ReflectionClass;
use ReflectionMethod;
use Validator;

/**
 * Class TestController
 * @package App\Http\Controllers
 */
class TestController extends Controller {
    
    use DetectsApplicationNamespace, ModelTrait;
    
    protected $keyId = 'LTAIk1710IrzHBg4';
    protected $keySecret = 'xxO5XaXx3O7kB3YR14XSdFulw1x56k';
    protected $callerShowNumber = '02388373982';
    
    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function index() {

        if (Request::method() == 'POST') {
            $pusher = new Pusher(
                '4e759473d69a97307905',
                'e51dbcffbb1250a2d98e',
                '583692',
                [
                    'cluster' => 'eu',
                    'encrypted' => true
                ]
            );
            $data['message'] = 'hello world';
            $pusher->trigger('my-channel', 'my-event', $data);
            
            return 'triggered';
        }
        
        return view('user.test');
        // $this->msSync();
        
    }
    
    public function apiCall() {
        
        try {
            $client = new Client();
            $reponse = $client->post(
                'http://sandbox.ddd/api/login', [
                    'form_params' => [
                        'username' => 'haoyuhang',
                        'password' => '******',
                    ],
                ]
            );
            $token = json_decode($reponse->getBody()->getContents())->{'token'};
            $response = $client->post(
                'http://sandbox.ddd/api/student_consumption', [
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
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listen() {
        
        return view('test.listen');
        
    }
    
    /**
     * @param ReflectionClass $class
     * @return mixed
     */
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
    
    /**
     * @param ReflectionClass $class
     * @return mixed
     */
    function getClassMethodsRefs(ReflectionClass $class) {
        
        return call_user_func_array(
            'array_merge',
            array_map(
                function (ReflectionMethod $method) { return [spl_object_hash($method) => $method->getName()]; },
                $class->getMethods()
            )
        );
        
    }
    
    /**
     * @param $id
     * @param $level
     * @return int
     */
    private function getLevel($id, &$level) {
        
        /** @var Department $parent */
        $parent = Department::find($id)->parent;
        if ($parent) {
            $level += 1;
            $this->getLevel($parent->id, $level);
        }
        
        return $level;
        
    }
    
    /**
     * 发送POST请求
     *
     * @param $url
     * @param mixed $formData
     * @return mixed|null
     * @throws Exception
     */
    private function curlPost($url, $formData) {
        
        $result = null;
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            // Check the return value of curl_exec(), too
            if (!$result) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
            curl_close($ch);
        } catch (Exception $e) {
            throw $e;
        }
        
        return $result;
        
    }
    
    /**
     * @throws \Pusher\PusherException
     */
    private function msSync() {
    
        $options = array(
            'cluster' => 'eu',
            'encrypted' => true
        );
        $pusher = new Pusher(
            '4e759473d69a97307905',
            'e51dbcffbb1250a2d98e',
            '583692',
            $options
        );
    
        $data['message'] = 'hello world';
        $pusher->trigger('my-channel', 'my-event', $data);
        # 创建学校
        $data = [
            'name' => '成都美视国际学校',
            'address' => '成都高新区人民南路南延线西侧',
            'signature' => '【成都美视国际学校】',
            'department_id' => 0,
            'corp_id' => 3,
            'menu_id' => 0,
            'school_type_id' => 1,
            'enabled' => 1,
        ];
        $rules = (new SchoolRequest)->rules();
        $validation = Validator::make($data, $rules);
        if ($validation->fails()) {
            dd($validation->errors());
        }
    
    
        # 同步现有部门
    
        # 同步现有会员
    
    
        exit;
        $corp = Corp::find(3);
        $token = Wechat::getAccessToken($corp->corpid, $corp->contact_sync_secret, true);
        $accessToken = $token['access_token'];
        // $result = json_decode(Wechat::getDeptList($accessToken), true);
        // $deparmtents = $result['department'];
        $result = json_decode(
            Wechat::getDeptUserDetail($accessToken, 1, 1), true
        );
        if ($result['errcode']) {
            echo 'wtf! ' . Constant::WXERR[$result['errcode']];
        }
        $users = $result['userlist'];
        dd($users);
        
    }
    
}
