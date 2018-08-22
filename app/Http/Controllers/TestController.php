<?php
namespace App\Http\Controllers;

use App\Facades\Wechat;
use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Message;
use Carbon\Carbon;
use Carbon\Translator;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\DetectsApplicationNamespace;
use ReflectionClass;
use ReflectionMethod;
use SimpleXMLElement;
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
    
        $corp = Corp::find(2);
        $token = Wechat::getAccessToken($corp->corpid, $corp->contact_sync_secret, true);
        $accessToken = $token['access_token'];
        $result = json_decode(Wechat::getDeptList($accessToken), true);
        $deparmtents = $result['department'];
        dd($deparmtents);
        
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
