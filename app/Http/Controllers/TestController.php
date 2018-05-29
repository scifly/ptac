<?php
namespace App\Http\Controllers;

use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Group;
use App\Models\Icon;
use App\Models\Menu;
use App\Models\MenuTab;
use App\Models\MenuType;
use App\Models\Message;
use App\Models\School;
use App\Models\Tab;
use App\Models\WapSite;
use App\Rules\Mobiles;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use ReflectionClass;
use ReflectionMethod;
use Validator;

/**
 * Class TestController
 * @package App\Http\Controllers
 */
class TestController extends Controller {
    
    use ModelTrait;
    
    protected $menus = [
        '首页' => [
            'id' => 0,
            'parent_id' => null,
            'uri' => 'home',
            'icon' => 'fa fa-home',
            'tabs' => []
        ],
        '消息中心' => [
            'id' => 0,
            'parent_id' => null,
            'uri' => 'messages/index',
            'icon' => 'fa fa-send-o',
            'tabs' => null
        ],
        '通讯录' => [
            'id' => 0,
            'parent_id' => null,
            'uri' => null,
            'icon' => 'fa fa-book',
            'tabs' => null
        ],
        '学生' => [
            'id' => 0,
            'parent_id' => '通讯录',
            'uri' => null,
            'icon' => 'fa fa-child',
            'tabs' => ['学生', '班级']
        ],
        '教职员工' => [
            'id' => 0,
            'parent_id' => '通讯录',
            'uri' => null,
            'icon' => 'fa fa-child',
            'tabs' => ['教职员工', '教职员工组']
        ],
        '监护人' => [
            'id' => 0,
            'parent_id' => '通讯录',
            'uri' => null,
            'icon' => 'fa fa-child',
            'tabs' => ['监护人', '学生']
        ],
        '考勤管理' => [
            'id' => 0,
            'parent_id' => null,
            'uri' => null,
            'icon' => 'fa fa-credit-card',
            'tabs' => null
        ],
        '考勤设置' => [
            'id' => 0,
            'parent_id' => '考勤管理',
            'uri' => null,
            'icon' => 'fa fa-credit-card',
            'tabs' => ['教职员工考勤设置', '学生考勤设置', '考勤机']
        ],
        '学生考勤查询统计' => [
            'id' => 0,
            'parent_id' => '考勤管理',
            'uri' => null,
            'icon' => 'fa fa-commenting',
            'tabs' => ['学生考勤记录']
        ],
        '教职员工考勤管理' => [
            'id' => 0,
            'parent_id' => '考勤管理',
            'uri' => null,
            'icon' => 'fa fa-commenting',
            'tabs' => ['教职员工考勤']
        ],
        '成绩中心' => [
            'id' => 0,
            'parent_id' => null,
            'uri' => null,
            'icon' => 'fa fa-file',
            'tabs' => null
        ],
        '考试设置' => [
            'id' => 0,
            'parent_id' => '成绩中心',
            'uri' => null,
            'icon' => null,
            'tabs' => ['考试', '考试类型']
        ],
        '成绩管理' => [
            'id' => 0,
            'parent_id' => '成绩中心',
            'uri' => null,
            'icon' => null,
            'tabs' => ['成绩', '成绩统计项', '总成绩']
        ],
        '自媒体管理' => [
            'id' => 0,
            'parent_id' => null,
            'uri' => null,
            'icon' => 'fa fa-photo',
            'tabs' => ['微网站栏目', '微网站文章', '微网站']
        ],
        '用户中心' => [
            'id' => 0,
            'parent_id' => null,
            'uri' => null,
            'icon' => 'fa fa-user',
            'tabs' => ['微网站栏目', '微网站文章', '微网站']
        ],
        '个人信息' => [
            'id' => 0,
            'parent_id' => '用户中心',
            'uri' => 'users/profile',
            'icon' => 'fa fa-user',
            'tabs' => null
        ],
        '重置密码' => [
            'id' => 0,
            'parent_id' => '用户中心',
            'uri' => 'users/reset',
            'icon' => 'fa fa-user',
            'tabs' => null
        ],
        '我的消息' => [
            'id' => 0,
            'parent_id' => '用户中心',
            'uri' => 'users/reset',
            'icon' => 'fa fa-user',
            'tabs' => null
        ],
        '系统设置' => [
            'id' => 0,
            'parent_id' => null,
            'uri' => null,
            'icon' => 'fa fa-gear',
            'tabs' => null
        ],
        '菜单管理' => [
            'id' => 0,
            'parent_id' => '系统设置',
            'uri' => null,
            'icon' => 'fa fa-list-ul',
            'tabs' => ['菜单']
        ],
        '部门管理' => [
            'id' => 0,
            'parent_id' => '系统设置',
            'uri' => null,
            'icon' => 'fa fa-sitemap',
            'tabs' => ['部门']
        ],
        '学校管理员' => [
            'id' => 0,
            'parent_id' => '系统设置',
            'uri' => null,
            'icon' => 'fa fa-user-plus',
            'tabs' => ['超级用户']
        ],
        '角色/权限' => [
            'id' => 0,
            'parent_id' => '系统设置',
            'uri' => null,
            'icon' => 'fa fa-meh-o',
            'tabs' => ['角色']
        ],
        '学校设置' => [
            'id' => 0,
            'parent_id' => '系统设置',
            'uri' => 'schools/edit/',
            'icon' => 'fa fa-university',
            'tabs' => null
        ],
        '年级/班级' => [
            'id' => 0,
            'parent_id' => '系统设置',
            'uri' => null,
            'icon' => 'fa fa-users',
            'tabs' => ['年级', '班级']
        ],
        '科目设置' => [
            'id' => 0,
            'parent_id' => '系统设置',
            'uri' => null,
            'icon' => 'fa fa-book',
            'tabs' => ['科目', '科目次分类']
        ],
        '学期设置' => [
            'id' => 0,
            'parent_id' => '系统设置',
            'uri' => null,
            'icon' => null,
            'tabs' => ['学期']
        ],
        '套餐设置' => [
            'id' => 0,
            'parent_id' => '系统设置',
            'uri' => null,
            'icon' => null,
            'tabs' => ['套餐类型']
        ],
    ];
    
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
     * @return bool
     * @throws Exception
     */
    public function index() {
    
    
        $sent = Message::whereSUserId(1)->get()
            ->unique('msl_id')->sortByDesc('created_at')
            ->groupBy('message_type_id');
        dd($sent->toArray());
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
