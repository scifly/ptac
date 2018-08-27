<?php
namespace App\Http\Controllers;

use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Http\Requests\SchoolRequest;
use App\Jobs\CreateSchool;
use App\Jobs\SyncDepartment;
use App\Jobs\SyncMember;
use App\Models\Corp;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\DepartmentUser;
use App\Models\Educator;
use App\Models\Group;
use App\Models\Menu;
use App\Models\Mobile;
use App\Models\School;
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
use Validator;

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

        try {
            DB::transaction(function () {
                $schoolDepartmentId = 33;
                $rootDepartmentId = 1175014494;
                $corp = Corp::find(2);
                $token = Wechat::getAccessToken($corp->corpid, $corp->contact_sync_secret, true);
                $accessToken = $token['access_token'];
                # 获取所有部门
                $result = json_decode(Wechat::getDeptList($accessToken), true);
                $departments = $result['department'];
                usort($departments, function($a, $b) {
                    return $a['id'] <=> $b['id'];
                });
                # 获取所有会员
                $result = json_decode(Wechat::getDeptUserDetail($accessToken, $rootDepartmentId, 1), true);
                $members = $result['userlist'];
                $departmentTypeId = DepartmentType::whereName('其他')->first()->id;
                $school = School::whereDepartmentId($schoolDepartmentId)->first();
                # 在学校对应的部门下创建现有部门
                foreach ($departments as &$department) {
                    $id = $department['id'];
                    $name = $department['name'];
                    $parentid = $department['parentid'];
                    $order = $department['order'];
                    if (!in_array($id, [$schoolDepartmentId, $rootDepartmentId])) {
                        if ($parentid == $rootDepartmentId) {
                            $parentId = $schoolDepartmentId;
                        } else {
                            $key = array_search($parentid, array_column($departments, 'id'));
                            $parentId = $departments[$key]['newid'];
                        }
                        $data = [
                            'name' => $name,
                            'parent_id' => $parentId,
                            'department_type_id' => $departmentTypeId,
                            'order' => $order,
                            'enabled' => 1
                        ];
                        $newDeptartment = (new Department)->store($data);
                        $department['newid'] = $newDeptartment->id;
                    } else {
                        $department['newid'] = $department['id'];
                    }
                }
                # 在本地创建所有会员（均为教职员工角色），并更新对应企业微信会员信息（所属部门）
                $groupId = Group::whereName('教职员工')->where('school_id', $school->id)->first()->id;
                foreach ($members as $member) {
                    # 创建本地用户
                    $user = User::create([
                        'username'     => $member['userid'],
                        'group_id'     => $groupId,
                        'password'     => bcrypt('12345678'),
                        'email'        => $member['email'],
                        'realname'     => $member['name'],
                        'avatar_url'   => $member['avatar'],
                        'gender'       => $member['gender'] == 1 ? 1 : 0,
                        'userid'       => $member['userid'],
                        'isleader'     => 0,
                        'position'     => $member['position'],
                        'english_name' => $member['english_name'],
                        'telephone'    => $member['telephone'],
                        'order'        => $member['order'],
                        'enabled'      => $member['enable'],
                        'synced'       => 1,
                        'subscribed'   => $member['status'] == 1 ? 1 : 0,
                    ]);
                    # 创建教职员工
                    Educator::create([
                        'user_id' => $user->id,
                        'school_id' => $school->id,
                        'sms_quote' => 0,
                        'enabled' => $user->enabled,
                    ]);
                    # 创建部门&用户绑定关系
                    $departmentIds = array_map(
                        function ($id) use ($departments) {
                            $key = array_search($id, array_column($departments, 'id'));
                            return $departments[$key]['newid'];
                        }, $member['department']
                    );
                    foreach ($departmentIds as $departmentId) {
                        DepartmentUser::create([
                            'user_id' => $user->id,
                            'department_id' => $departmentId,
                            'enabled' => $user->enabled,
                        ]);
                    }
                    # 保存用户默认手机号码
                    Mobile::create([
                        'user_id' => $user->id,
                        'mobile' => $member['mobile'],
                        'isdefault' => 1,
                        'enabled' => 1
                    ]);
                    # 更新用户对应的企业微信会员信息（所属部门）
                    $data = [
                        'corpIds'      => [$school->corp_id],
                        'userid'       => $user->userid,
                        'department'   => $departmentIds,
                    ];
                    SyncMember::dispatch($data, null, 'update');
                }
                # 删除所有手动创建的部门
                for ($i = count($departments) - 1; $i >= 0; $i--) {
                    $data = ['id' => $departments[$i]['id'], 'corp_id' => $school->corp_id];
                    SyncDepartment::dispatch($data, null, 'delete');
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return 'done';
        
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
    
}
