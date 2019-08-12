<?php
namespace App\Http\Controllers;

use App\Facades\Wechat;
use App\Helpers\{Broadcaster, HttpStatusCode, ModelTrait};
use App\Models\{Corp, Department};
use Auth;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\{DB, Storage};
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
    const BASEURI = [
        'ent' => 'https://qyapi.weixin.qq.com/cgi-bin/',                    # 企业微信
        'red' => 'https://api.mch.weixin.qq.com/',                          # 企业微信 - 企业支付
        'url' => 'https://open.weixin.qq.com/connect/oauth2/authorize?',    # 企业微信 - 获取code
        
        'pub' => 'https://api.weixin.qq.com/cgi-bin/',                      # 微信公众号
        'dat' => 'https://api.weixin.qq.com/',                              # 微信公众号 - 数据统计
        'svc' => 'https://api.weixin.qq.com/customservice/',                # 微信公众号 - 客服账号管理
    ];
    const APIS = [
        'ent' => [
            'agent' => [
                'get' => [1, 'agentid'],
                'set' => 1,
            ],
            'appchat'         => [
                'create' => 1,
                'update' => 1,
                'get'    => 1,
                'send'   => 1,
            ],
            'card'            => [
                'invoice/reimburse/getinvoiceinfo'      => 1,
                'invoice/reimburse/updateinvoicestatus' => 1,
                'invoice/reimburse/updatestatusbatch'   => 1,
                'invoice/reimburse/getinvoiceinfobatch' => 1,
            ],
            'checkin'         => [
                'getcheckindata'   => 1,
                'getcheckinoption' => 1,
            ],
            'batch'           => [
                'invite'       => 1,
                'syncuser'     => 1,
                'replaceuser'  => 1,
                'replaceparty' => 1,
                'getresult'    => 1,
            ],
            'corp'            => [
                'get_join_qrcode' => [1, 'size_type'],
                'getapprovaldata' => 1,
            ],
            'department'      => [
                'create' => 1,
                'update' => 1,
                'delete' => [1, 'id'],
                'list'   => [1, 'id'],
            ],
            'dial'            => [
                'get_dial_record' => 1,
            ],
            'externalcontact' => [
                'get_fellow_user_list'   => 1,
                'list'                   => [1, 'userid'],
                'get'                    => [1, 'external_userid'],
                'add_contact_way'        => 1,
                'add_msg_template'       => 1,
                'get_group_msg_result'   => 1,
                'get_user_behavior_data' => 1,
                'send_welcome_msg'       => 1,
                'get_unassigned_list'    => 1,
                'transfer'               => 1,
                'get_corp_tag_list'      => 1,
                'mark_tag'               => 1,
            ],
            'gettoken'        => [
                'gettoken' => ['corpid', 'corpsecret'],
            ],
            'linkedcorp'      => [
                'message/send' => 1,
            ],
            'media'           => [
                'upload'    => [1, 'type'],
                'uploadimg' => 1,
                'get'       => [1, 'media_id'],
                'get/jssdk' => [1, 'media_id'],
            ],
            'menu'            => [
                'create' => [1, 'agentid'],
                'get'    => [1, 'agentid'],
                'delete' => [1, 'agentid'],
            ],
            'message'         => [
                'send' => 1,
            ],
            'tag'             => [
                'create'      => 1,
                'update'      => 1,
                'delete'      => [1, 'tagid'],
                'get'         => [1, 'tagid'],
                'addtagusers' => 1,
                'deltagusers' => 1,
                'list'        => 1,
            ],
            'user'            => [
                'create'            => 1,
                'get'               => [1, 'userid'],
                'update'            => 1,
                'delete'            => [1, 'userid'],
                'batchdelete'       => 1,
                'simplelist'        => [1, 'department_id', 'fetch_child'],
                'list'              => [1, 'department_id', 'fetch_child'],
                'convert_to_openid' => 1,
                'convert_to_userid' => 1,
                'authsucc'          => [1, 'userid'],
                'getuserinfo'       => [1, 'code'],
            ],
        ],
        'red' => [
            'mmpaymkttransfers' => [
                'sendworkwxredpack'     => 0,
                'queryworkwxredpack'    => 0,
                'paywwsptrans2pocket'   => 0,
                'querywwsptrans2pocket' => 0,
            ],
        ],
        'pub' => [
            'token'                     => [
                'token' => ['grant_type', 'appid', 'secret'],
            ],
            'menu'                      => [
                'create'         => 1,
                'get'            => 1,
                'delete'         => 1,
                'addconditional' => 1,
            ],
            'get_current_selfmenu_info' => [
                'get_current_selfmenu_info' => 1,
            ],
            'material'                  => [
                'add_news'          => 1,
                'update_news'       => 1,
                'get_material'      => ['accesstoken'],
                'batchget_material' => ['accesstoken'],
                'get_materialcount' => ['accesstoken'],
                'del_material'      => ['accesstoken'],
            ],
            'media'                     => [
                'uploadimg'  => 1,
                'uploadnews' => 1,
                'upload'     => [1, 'type'],
                'get'        => [1, 'media_id'],
            ],
            'message'                   => [
                'mass/sendall'       => 1,
                'mass/send'          => 1,
                'mass/delete'        => 1,
                'mass/preview'       => 1,
                'mass/get'           => 1,
                'mass/speed/get'     => 1,
                'template/send'      => 1,
                'template/subscribe' => 1,
            ],
            'qrcode'                    => [
                'create' => 1,
            ],
            'shorturl'                  => [
                'shorturl' => 1,
            ],
            'tags'                      => [
                'create'                 => 1,
                'get'                    => 1,
                'update'                 => 1,
                'delete'                 => 1,
                'members/batchtagging'   => 1,
                'members/batchuntagging' => 1,
                'getidlist'              => 1,
                'members/getblacklist'   => 1
            ],
            'template'                  => [
                'api_set_industry'         => 1,
                'get_industry'             => 1,
                'api_add_template'         => 1,
                'get_all_private_template' => 1,
                'del_private_template'     => 1,
            ],
            'user'                      => [
                'tag/get'           => 1,
                'info/updateremark' => 1,
                'info'              => [1, 'openid', 'lang'],
                'get'               => [1, 'next_openid']
            ],
        ],
        'dat' => [
            'datacube' => [
                'getusersummary'          => 1,
                'getusercumulate'         => 1,
                'getarticlesummary'       => 1,
                'getarticletotal'         => 1,
                'getuserread'             => 1,
                'getuserreadhour'         => 1,
                'getusershare'            => 1,
                'getusersharehour'        => 1,
                'getupstreammsg'          => 1,
                'getupstreammsghour'      => 1,
                'getupstreammsgweek'      => 1,
                'getupstreammsgmonth'     => 1,
                'getupstreammsgdist'      => 1,
                'getupstreammsgdiskweek'  => 1,
                'getupstreammsgdiskmoth'  => 1,
                'getinterfacesummary'     => 1,
                'getinterfacesummaryhour' => 1,
            ]
        ],
        'svc' => [
            'kfaccount/add'           => 1,
            'kfaccount/update'        => 1,
            'kfaccount/del'           => 1,
            'kfaccount/uploadheadimg' => [1, 'kf_account'],
            'getkflist'               => 1,
        ],
    ];
    
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
    
        $urls = [];
        foreach (self::APIS as $platform => $apis) {
            $baseUri = self::BASEURI[$platform];
            foreach ($apis as $category => $methods) {
                foreach ($methods as $method => $params) {
                    if (is_array($params)) {
                        $params[0] = 'access_token';
                    } else {
                        $params = !$params ? [] :  ['access_token'];
                    }
                    $data = [];
                    foreach ($params as $param) {
                        $data[$param] = rand(5, 15);
                    }
                    $urls[] = join([
                        $baseUri, $category, '/', $method, '?', http_build_query($data)
                    ]);
                }
            }
        }
        
        dd($urls);
        
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
                $token = Wechat::token($corp->corpid, $corp->contact_sync_secret, true);
                $accessToken = $token['access_token'];
                $result = json_decode(
                    Wechat::invoke(
                        'ent', 'department', 'list',
                        [$accessToken, null]
                    ), true
                );
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
        // $token = Wechat::token($corp->corpid, $corp->contact_sync_secret, true);
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
