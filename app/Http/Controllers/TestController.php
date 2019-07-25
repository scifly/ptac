<?php
namespace App\Http\Controllers;

use App\Facades\Wechat;
use App\Helpers\{Broadcaster, HttpStatusCode, ModelTrait};
use App\Models\{Corp, Department};
use Auth;
use Doctrine\Common\Inflector\Inflector;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\{DB, Request, Storage};
use Illuminate\View\View;
use Pusher\Pusher;
use Pusher\PusherException;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

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
    
        $params = [
            'uuid'         => '113',
            'name'         => '测试人员',
            'age'          => 0,
            'sex'          => 1,
            'role'         => 1,
            'identity_num' => '',
            'csOther'      => '',
            'csICCard'     => '',
            'csTel'        => '',
            'csDep'        => '',
            'pStr'         => base64_encode(Storage::disk('uploads')->get('2019/07/23/5d367afcc87a9-罗焱21级1班男.JPG'))
        ];
        echo json_encode($params, JSON_UNESCAPED_UNICODE);
        exit;
        
        dd(Request::input('abcdefg'));
        
        if (Request::method() == 'POST') {
            $abc = Request::file('abc');
            $def = Request::file('def');
            echo $abc->getFilename() . ' : ' . $def->getFilename();
            return dd(Request::all());
        } else {
            return view('user.test');
        }
        $client = new Client;
        $response = $client->post(
            'http://127.0.0.1:18080/GetDeviceList?username=admin&password=admin'
        );
        dd(json_decode($response->getBody(), true));
        (new Corp)->index();
        phpinfo();
        exit;
        dd(ucfirst(Inflector::camelize('passage_rule')));
        // return view('user.index');
        // $server = "120.78.55.152";
        // $port = 9933;
        // if (!($sock = socket_create(AF_INET, SOCK_DGRAM, 0))) {
        //     $errcode = socket_last_error();
        //     $errmsg = socket_strerror($errcode);
        //     die ("Couldn't create socket: [$errcode] $errmsg\n");
        // }
        // echo "Socket created \n";
        // $input = 'abcdefg';
        // if( ! socket_sendto($sock, $input , strlen($input) , 0 , $server , $port)) {
        //     $errorcode = socket_last_error();
        //     $errormsg = socket_strerror($errorcode);
        //
        //     die("Could not send data: [$errorcode] $errormsg \n");
        // }
        // // # Now receive reply from server and print it
        // if(socket_recv($sock , $reply , 2045 , MSG_PEEK ) === FALSE) {
        //     $errorcode = socket_last_error();
        //     $errormsg = socket_strerror($errorcode);
        //
        //     die("Could not receive data: [$errorcode] $errormsg \n");
        // }
        //
        // echo "Reply : $reply";
        error_reporting(~E_WARNING);
        $server = '119.23.73.0';
        $port = 10000;
    
        if(!($sock = socket_create(AF_INET, SOCK_DGRAM, 0)))
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
        
            die("Couldn't create socket: [$errorcode] $errormsg \n");
        }
    
        echo "Socket created \n";
    
        while(1)
        {
            //Take some input to send
            // echo 'Enter a message to send : ';
            // $input = fgets(STDIN);

            //Send the message to the server
            $input = 'abcdefg';
            if( ! socket_sendto($sock, $input , strlen($input) , 0 , $server , $port))
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
            
                die("Could not send data: [$errorcode] $errormsg \n");
            }

            //Now receive reply from server and print it
            if(socket_recv ( $sock , $reply , 2045 , MSG_PEEK ) === FALSE)
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
            
                die("Could not receive data: [$errorcode] $errormsg \n");
            }
        
            echo "Reply : $reply";
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
            'statusCode' => HttpStatusCode::OK,
            'message'    => '工作正常',
        ]);
        
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
     * @throws PusherException
     * @throws Throwable
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
     * @throws PusherException
     */
    private function inform($message) {
    
        $data['message'] = $message;
        $this->pusher->trigger('my-channel', 'my-event', $data);
        
    }
    
    /**
     * @param $tags
     */
    private function formatTags(&$tags) {

        foreach ($tags as &$tag) {
            $tag['a'] = $tag['a'] . '.tag';
        }
        
    }
    
}
