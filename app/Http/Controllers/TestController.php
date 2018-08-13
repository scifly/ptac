<?php
namespace App\Http\Controllers;

use App\Helpers\ModelTrait;
use App\Models\Department;
use App\Models\Message;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\DetectsApplicationNamespace;
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
     */
    public function index() {
        
        $messages = Message::where('title', 'like', '%图片%')->toSql();
        
        dd($messages);
        
        $rules = [
            'student_number' => 'required|string|between:5,32',
            'punch_time'     => 'required|date',
            'inorout'        => 'required|integer',
            'media_id'       => 'required|integer',
            'longitude'      => 'required|numeric',
            'latitude'       => 'required|numeric',
            'machineid'      => 'required|string|between:1,20',
        ];
        $datum = [
            'student_number' => '200000',
            'punch_time' => '2018-03-20 10:12',
            'inorout' => 0,
            'machineid' => '1',
            'longitude' => 0,
            'latitude' => 0,
            'media_id' => 0
        ];
        $result = Validator::make($datum, $rules);
        dd($result->errors());
        
    
        $client = new Client();
        $reponse = $client->post(
            'http://weixin.028lk.com/api/student_attendance', [
                'form_params' => [
                    'ab' => 1
                ],
            ]
        );
        dd($reponse);
        
        $a = [];
        dd($a[0]);
        
        $appid = '5100000025';
        $appsecret = 'B4C6F3A34F5936CEBA92C008F12B0396';
        $nonce = '15';
        $method = '10';
        $url = 'http://eccard.eicp.net:8078/Dispatch.aspx';
        
        $data = urlencode(
            base64_encode(
                json_encode([
                    'dname' => '小学一年级',
                    'dfather' => '成都外国语学校',
                    'dexpiration' => '2020-08-05 23:59:59',
                    'dnumber' => '2000',
                    'dtel' => '18030718323'
                ])
            )
        );
        ;
        $hash = substr(
            strtoupper(
                base64_encode(
                    md5($appid . '|' . $appsecret . '|' . $data . '|' . $nonce)
                )
            ),
            0, 24
        );
        
        $formData = json_encode([
            'appid' => $appid,
            'hash' => $hash,
            'method' => $method,
            'data' => $data,
            'nonce' => $nonce
        ]);
        
        dd($this->curlPost($url, $formData));
        
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
    
}
