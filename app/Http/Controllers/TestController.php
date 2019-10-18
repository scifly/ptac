<?php
namespace App\Http\Controllers;

use App\Helpers\{Broadcaster, Constant, ModelTrait};
use App\Models\{Department, User};
use Auth;
use Doctrine\Common\Inflector\Inflector;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Pusher\Pusher;
use Pusher\PusherException;
use ReflectionClass;
use ReflectionMethod;
use Schema;
use Throwable;

/**
 * Class TestController
 * @package App\Http\Controllers
 */
class TestController extends Controller {
    
    use ModelTrait;
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
     * @throws PusherException
     */
    function __construct() {
        
        $this->pusher = new Pusher(
            self::KEY, self::SECRET, self::APP_ID,
            ['cluster' => self::CLUSTER, 'encrypted' => true]
        );
        
    }
    
    
    
    /**
     * @throws Exception
     * @throws Throwable
     */
    public function index() {
    
        /** @var \Illuminate\Routing\Route $route */
        foreach (Route::getRoutes() as $route) {
            
            echo $route->controller;
            
        };
        exit;
    
    }
    
    function foreignKeys() {
    
        $field = Request::query('f');
        $tables = DB::select('SHOW TABLES;');
        foreach ($tables as $table) {
            $t = current($table);
            if (Schema::hasColumn($t, $field)) {
                echo Inflector::classify(Inflector::singularize($t)) . '<br />';
            }
        }
        
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
            $response->getHeader('status');
            dd(json_decode($response->getBody(), true));
        } catch (ClientException $e) {
            echo $e->getResponse()->getStatusCode();
            echo $e->getResponse()->getBody()->getContents();
        }
        
    }
    
    /**
     * @return Factory|View
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
     * @throws PusherException
     */
    function event() {
        
        (new Broadcaster)->broadcast([
            'userId'     => Auth::id() ?? 1,
            'title'      => '广播测试',
            'statusCode' => Constant::OK,
            'message'    => '工作正常',
        ]);
        
    }
    
    /**
     * @param $id
     * @param $level
     * @return int
     */
    function getLevel($id, &$level) {
        
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
    function curlPost($url, $formData) {
        
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
     * 发送广播消息
     *
     * @param $message
     * @throws PusherException
     */
    function inform($message) {
        
        $data['message'] = $message;
        $this->pusher->trigger('my-channel', 'my-event', $data);
        
    }
    
    /**
     * @param $tags
     */
    function formatTags(&$tags) {
        
        foreach ($tags as &$tag) {
            $tag['a'] = $tag['a'] . '.tag';
        }
        
    }
    
    /**
     * @param $id
     * @param $callback
     */
    function test($id, $callback) {
        
        $a = $id * 4;
        $callback($a);
        
    }
    
    /**
     * @return Factory|JsonResponse|View
     */
    function ajaxSelect() {
        
        (new Department)->parentIds(39);
        if (Request::ajax()) {
            $data = [];
            $keyword = Request::query('term');
            $users = User::where('username', 'like', '%' . $keyword . '%')
                ->pluck('realname', 'id');
            foreach ($users as $id => $text) {
                $data['results'][] = [
                    'id' => $id,
                    'text' => $text,
                ];
            }
            return response()->json($data);
        }
        
        return view('user.test');
        
    }
    
}
