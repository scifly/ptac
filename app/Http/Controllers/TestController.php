<?php
namespace App\Http\Controllers;

use App\Apis\Kinder;
use App\Facades\Wechat;
use App\Helpers\ModelTrait;
use App\Models\Action;
use App\Models\CommType;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Pusher\Pusher;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class TestController
 * @package App\Http\Controllers
 */
class TestController extends Controller {
    
    use DetectsApplicationNamespace, ModelTrait;
    protected $pusher;
    protected $keyId = 'LTAIk1710IrzHBg4';
    protected $keySecret = 'xxO5XaXx3O7kB3YR14XSdFulw1x56k';
    protected $callerShowNumber = '02388373982';
    const APP_ID = '583692';
    const KEY = '4e759473d69a97307905';
    const SECRET = 'e51dbcffbb1250a2d98e';
    const CLUSTER = 'eu';
    
    /**
     * TestController constructor.
     * @throws \Pusher\PusherException
     */
    function __construct() {
        
        $this->pusher = new Pusher(
            self::KEY, self::SECRET, self::APP_ID,
            ['cluster' => self::CLUSTER, 'encrypted' => true]
        );
        
    }
    
    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function index() {

        $n = new ReflectionClass('App\Controllers\ActionController');
        dd($n->getProperty('type'));
        $comm = [null => '全部'];
        $comm = array_merge(
            $comm,
            CommType::all()->pluck('name', 'id')->toArray()
        );
        dd($comm);
        if (Request::method() == 'POST') {
            // $department = new Department;
            // $subs = $department->whereIn('id', $department->subDepartmentIds(33))->get()->toArray();
            // $response = ['userId' => null];
            // foreach ($subs as $sub) {
            //     $sub['parentid'] = $sub['parent_id'] == 33 ? 10000 : $sub['parent_id'] + 10000;
            //     $kd = new Kinder('部门', 'create', $sub, $response);
            //     $result = $kd->sync();
            //     $this->inform($result['msg'] . ' : ' . $sub['name'] . ' : ' . $sub['id']);
            //     unset($kd);
            // }
            try {
                DB::transaction(function () {
                    $studentUserIds = Student::whereRemark('导入')->pluck('user_id')->toArray();
                    $users = User::whereIn('id', $studentUserIds)->get();
    
                    // $users = User::whereGroupId(11)->where('id', '>', 800)->get()->toArray();
                    $response = ['userId' => null];
                    /** @var User $user */
                    foreach ($users as $user) {
                        # 同步学生
                        $user->{'name'} = $user->realname;
                        $user->{'remark'} = $user->student->oncampus;
                        $kd = new Kinder('人员', 'create', $user->toArray(), $response);
                        $result = $kd->sync();
                        $this->inform(
                            $result['code'] . ' : ' .
                            $result['msg'] . ' : ' .
                            $user->realname . ' : ' .
                            $user->id . ' : 学生'
                        );
                        unset($kd);
                        # 同步监护人
                        /** @var User $custodianUser */
                        $custodians = $user->student->custodians;
                        if ($custodians->isEmpty()) { continue; }
                        $custodianUser = $custodians->first()->user;
                        $custodianUser->{'name'} = $custodianUser->realname;
                        $custodianUser->{'remark'} = $user->realname . '.' . $user->student->student_number;
                        $kd = new Kinder('人员', 'create', $custodianUser->toArray(), $response);
                        $result = $kd->sync();
                        $this->inform(
                            $result['code'] . ' : ' .
                            $result['msg'] . ' : ' .
                            $custodianUser->realname . ' : ' .
                            $custodianUser->id . ' : 监护人'
                        );
                        unset($kd);
                    }
                });
            } catch (Exception $e) {
                $this->inform($e->getMessage());
            }
            
        }
    
        return view('user.test');
        
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
     * @throws \Throwable
     */
    private function msSync() {
    
        $pusher = new Pusher(
            self::KEY, self::SECRET, self::APP_ID,
            ['cluster' => self::CLUSTER, 'encrypted' => true]
        );
    
        try {
            DB::transaction(function () use ($pusher) {
                $corp = Corp::find(3);
                $token = Wechat::getAccessToken($corp->corpid, $corp->contact_sync_secret, true);
                $accessToken = $token['access_token'];
                $result = json_decode(Wechat::getDeptList($accessToken), true);
                $deparmtents = $result['department'];
                usort($deparmtents, function($a, $b) {
                    return $a['id'] <=> $b['id'];
                });
                // $result = json_decode(
                //     Wechat::getDeptUserDetail($accessToken, 1, 1), true
                // );
                // if ($result['errcode']) {
                //     echo 'wtf! ' . Constant::WXERR[$result['errcode']];
                // }
                // $users = $result['userlist'];
                return $deparmtents;
            });
        } catch (Exception $e) {
            $this->inform($e->getMessage());
        }
        // return true;
        
    
        # 同步现有部门
    
        # 同步现有会员
    
    
        // $corp = Corp::find(3);
        // $token = Wechat::getAccessToken($corp->corpid, $corp->contact_sync_secret, true);
        // $accessToken = $token['access_token'];
        // // $result = json_decode(Wechat::getDeptList($accessToken), true);
        // // $deparmtents = $result['department'];
        // $result = json_decode(
        //     Wechat::getDeptUserDetail($accessToken, 1, 1), true
        // );
        // if ($result['errcode']) {
        //     echo 'wtf! ' . Constant::WXERR[$result['errcode']];
        // }
        // $users = $result['userlist'];
        // dd($users);
        
    }
    
    /**
     * 发送广播消息
     *
     * @param $message
     * @throws \Pusher\PusherException
     */
    private function inform($message) {
    
        $data['message'] = $message;
        $this->pusher->trigger('my-channel', 'my-event', $data);
        
    }
    
    private function formatTags(&$tags) {

        foreach ($tags as &$tag) {
            $tag['a'] = $tag['a'] . '.tag';
        }
        
    }
    
}
