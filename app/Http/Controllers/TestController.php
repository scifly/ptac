<?php
namespace App\Http\Controllers;

use App\Facades\Wechat;
use App\Helpers\ModelTrait;
use App\Models\Action;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Group;
use App\Models\Message;
use App\Models\Mobile;
use App\Models\User;
use App\Services\Test;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class TestController
 * @package App\Http\Controllers
 */
class TestController extends Controller {
    
    use ModelTrait;
    
    const ALLOWED_CORP_ACTIONS = [
        '/corps/edit/%s',
        '/corps/update/%s',
    ];
    const ALLOWED_SCHOOL_ACTIONS = [
        '/schools/show/%s',
        '/schools/edit/%s',
        '/schools/update/%s',
    ];
    const ALLOWED_WAPSITE_ACTIONS = [
        '/wap_sites/show/%s',
        '/wap_sites/edit/%s',
        '/wap_sites/update/%s',
    ];
    
    const EXPORT_RANGES = [
        'class' => 0,
        'grade' => 1,
        'all'   => 2
    ];
    
    protected $department;
    
    /**
     * @return mixed|null
     * @throws Exception
     */
    public function index() {
    
        $result = null;
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://192.168.5.14:2018/Api/Execute');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'Command' => '<Root><Function>SendMessage</Function><LoginName>test</LoginName><Password>link123</Password ><SignedData></SignedData><RequestData><Title>EUCP</Title><MessageType>0</MessageType><ReceiveNum>15881004695</ReceiveNum><Content>您好，您的验证码是680680【技术部】</Content ></RequestData></Root>'
            ]);
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
    
    
        $messages = Message::all();
        foreach ($messages as $key => $message) {
            echo $key . ' : ' . $message->id . '<br />';
        }
        die;
        $corpid = 'wxe75227cead6b8aec';
        $secret = 'uorwAVlN3_EU31CDX0X1oQJk9lB0Or41juMH-cLcIE';
        $token = Wechat::getAccessToken($corpid, $secret, true);
        $agentid = 1000007;
        dd(Wechat::getApp($token, $agentid));
        $a = [
            1 => 'ab',
            2 => 'cd',
            3 => 'ef'
        ];
        $s = array_slice($a, 1, 1, true);
        dd($s[key($s)]);
        $names = ['运营', '企业', '学校'];
        $arrs = array_map(function ($name) {
            return [$name => Group::whereName($name)->first()->id];
        }, $names);
        $arrs = Group::whereIn('name', ['运营', '企业', '学校'])->get()->pluck('name', 'id')->toArray();
        dd(($arrs));
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
            dd(json_decode($reponse->getBody(), true));
        } catch (ClientException $e) {
            echo $e->getResponse()->getStatusCode();
            echo $e->getResponse()->getBody()->getContents();
        }
        
    }
    
    private function wtf() {
        
        dd(debug_backtrace());
        
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
    
}
