<?php
namespace App\Http\Controllers;

use App\Apis\Kinder;
use App\Facades\Wechat;
use App\Helpers\ModelTrait;
use App\Models\{Action, Corp, Department, Media, MediaType, Message, Student, Tab, User};
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Facades\{Auth, DB, Request, Route};
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
    
        $dir = public_path() . '/uploads/' . date('Y/m/d/');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        exit;
        
        // $d = new Department();
        // $departmentId = School::find($this->schoolId())->department_id;
        // $departmentIds = array_merge(
        //     [$departmentId],
        //     $d->subDepartmentIds($departmentId)
        // );
        $departmentIds = $this->departmentIds(Auth::id());
        $records = [];
        $department = new Department();
        foreach ($departmentIds as $departmentId) {
            $eUsers = Department::find($departmentId)->users->sortBy('created_at')->filter(
                function(User $user) {
                    return $user->educator ? true : false;
                }
            );
            /** @var User $user */
            foreach ($eUsers as $user) {
                $path = [];
                $dept = str_replace(
                    '部门 . 凌凯科技有限公司 . 成都美视国际学校 . 成都美视国际学校 . ', '',
                    $department->leafPath($departmentId, $path)
                );
                $records[] = [
                    $user->username,
                    $user->realname,
                    $user->mobiles->where('isdefault', 1)->first()->mobile,
                    $user->position,
                    $user->created_at,
                    $user->updated_at,
                    $dept
                ];
            }
        }
        dd($records);
    
        dd(MediaType::all()->pluck('remark', 'id')->toArray());
        try {
            DB::transaction(function() {
                $messages = Message::all();
                foreach ($messages as $message) {
                    $content = json_decode($message->content, true);
                    $msgType = $content['msgtype'];
                    if ($msgType == 'sms') {
                        $msgType = 'text';
                    }
                    $message->media_type_id = MediaType::whereName($msgType)->first()->id;
                    if ($message->event_id) {
                        $message->sent = 2;
                    }
                    $message->save();
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        exit;
        try {
            DB::transaction(function () {
                $medias = Media::all();
                foreach ($medias as $media) {
                    $paths = explode('.', $media->path);
                    $type = $paths[count($paths) - 1];
                    $mediaType = 'file';
                    if (in_array($type, ['jpg', 'png'])) {
                        $mediaType = 'image';
                    } elseif ($type == 'amr') {
                        $mediaType = 'voice';
                    } elseif ($type == 'mp4') {
                        $mediaType = 'video';
                    }
                    $media->media_type_id = MediaType::whereName($mediaType)->first()->id;
                    $media->save();
                }
                
            });
        } catch (Exception $e) {
            throw $e;
        }
        // $types = [];
        // foreach ($medias as $media) {
        //     $paths = explode('.', $media->path);
        //     $types[] = $paths[count($paths) - 1];
        // }
        // dd(array_unique($types));
        //
        // return view('user.test', [
        //     'a' => ['c' => 1, 'b' => 2],
        //     'b' => 2
        // ]);
        exit;
        try {
            DB::transaction(function () {
                $tabs = Tab::all();
                foreach ($tabs as $tab) {
                    $comment = $tab->comment;
                    $name = $tab->name;
                    $tab->name = $comment;
                    $tab->comment = $name;
                    $tab->save();
                }
                $actions = Action::all();
                foreach ($actions as $action) {
                    $action->tab_id = Tab::whereName($action->controller)->first()->id;
                    $action->save();
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        dd('done');
        $routes = Route::getRoutes()->getRoutes();
        /** @var \Illuminate\Routing\Route $route */
        $uris = [];
        foreach ($routes as $route) {
            if (isset($route->action['controller'])) {
                echo ($route->action['controller']);
            } else {
                $uris[] = $route->uri;
            }
        }
        dd($uris);
        exit;
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
