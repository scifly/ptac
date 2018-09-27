<?php
namespace App\Jobs;

use App\Events\JobResponse;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Models\Action;
use App\Models\Group;
use App\Models\Icon;
use App\Models\Menu;
use App\Models\MenuTab;
use App\Models\MenuType;
use App\Models\School;
use App\Models\Tab;
use App\Models\WapSite;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class CreateSchoolMenu
 * @package App\Jobs
 */
class CreateSchool implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $parentMenu, $menuNameTab, $menu, $menuTab;
    
    protected $menus = [
        '首页'       => [
            'id'        => 0,
            'parent_id' => null,
            'uri'       => 'home',
            'icon'      => 'fa fa-home',
            'tabs'      => [],
        ],
        '消息中心'     => [
            'id'        => 0,
            'parent_id' => null,
            'uri'       => 'messages/index',
            'icon'      => 'fa fa-send-o',
            'tabs'      => null,
        ],
        '通讯录'      => [
            'id'        => 0,
            'parent_id' => null,
            'uri'       => null,
            'icon'      => 'fa fa-book',
            'tabs'      => null,
        ],
        '学生'       => [
            'id'        => 0,
            'parent_id' => '通讯录',
            'uri'       => null,
            'icon'      => 'fa fa-child',
            'tabs'      => ['学生', '班级'],
        ],
        '教职员工'     => [
            'id'        => 0,
            'parent_id' => '通讯录',
            'uri'       => null,
            'icon'      => 'fa fa-user',
            'tabs'      => ['教职员工', '教职员工组'],
        ],
        '监护人'      => [
            'id'        => 0,
            'parent_id' => '通讯录',
            'uri'       => null,
            'icon'      => 'fa fa-user',
            'tabs'      => ['监护人', '学生'],
        ],
        '标签'       => [
            'id'        => 0,
            'parent_id' => '通讯录',
            'uri'       => null,
            'icon'      => 'fa fa-tags',
            'tabs'      => ['标签管理'],
        ],
        '考勤管理'     => [
            'id'        => 0,
            'parent_id' => null,
            'uri'       => null,
            'icon'      => 'fa fa-credit-card',
            'tabs'      => null,
        ],
        '考勤设置'     => [
            'id'        => 0,
            'parent_id' => '考勤管理',
            'uri'       => null,
            'icon'      => 'fa fa-credit-card',
            'tabs'      => ['教职员工考勤设置', '学生考勤设置', '考勤机'],
        ],
        '学生考勤查询统计' => [
            'id'        => 0,
            'parent_id' => '考勤管理',
            'uri'       => null,
            'icon'      => 'fa fa-commenting',
            'tabs'      => ['学生考勤记录'],
        ],
        '教职员工考勤管理' => [
            'id'        => 0,
            'parent_id' => '考勤管理',
            'uri'       => null,
            'icon'      => 'fa fa-commenting',
            'tabs'      => ['教职员工考勤'],
        ],
        '学生消费管理'   => [
            'id'        => 0,
            'parent_id' => null,
            'uri'       => null,
            'icon'      => 'fa fa-money',
            'tabs'      => ['消费记录'],
        ],
        '成绩中心'     => [
            'id'        => 0,
            'parent_id' => null,
            'uri'       => null,
            'icon'      => 'fa fa-file',
            'tabs'      => null,
        ],
        '考试设置'     => [
            'id'        => 0,
            'parent_id' => '成绩中心',
            'uri'       => null,
            'icon'      => null,
            'tabs'      => ['考试', '考试类型'],
        ],
        '成绩管理'     => [
            'id'        => 0,
            'parent_id' => '成绩中心',
            'uri'       => null,
            'icon'      => null,
            'tabs'      => ['成绩', '成绩统计项', '总成绩'],
        ],
        '自媒体管理'    => [
            'id'        => 0,
            'parent_id' => null,
            'uri'       => null,
            'icon'      => 'fa fa-photo',
            'tabs'      => ['微网站栏目', '微网站文章', '微网站'],
        ],
        '用户中心'     => [
            'id'        => 0,
            'parent_id' => null,
            'uri'       => null,
            'icon'      => 'fa fa-user',
            'tabs'      => null,
        ],
        '个人信息'     => [
            'id'        => 0,
            'parent_id' => '用户中心',
            'uri'       => 'users/edit',
            'icon'      => 'fa fa-user',
            'tabs'      => null,
        ],
        '重置密码'     => [
            'id'        => 0,
            'parent_id' => '用户中心',
            'uri'       => 'users/reset',
            'icon'      => 'fa fa-user',
            'tabs'      => null,
        ],
        '我的消息'     => [
            'id'        => 0,
            'parent_id' => '用户中心',
            'uri'       => 'users/message',
            'icon'      => 'fa fa-user',
            'tabs'      => null,
        ],
        '待办事项'     => [
            'id'        => 0,
            'parent_id' => '用户中心',
            'uri'       => 'users/event',
            'icon'      => 'fa fa-user',
            'tabs'      => null,
        ],
        '系统设置'     => [
            'id'        => 0,
            'parent_id' => null,
            'uri'       => null,
            'icon'      => 'fa fa-gear',
            'tabs'      => null,
        ],
        '菜单管理'     => [
            'id'        => 0,
            'parent_id' => '系统设置',
            'uri'       => null,
            'icon'      => 'fa fa-list-ul',
            'tabs'      => ['菜单'],
        ],
        '部门管理'     => [
            'id'        => 0,
            'parent_id' => '系统设置',
            'uri'       => null,
            'icon'      => 'fa fa-sitemap',
            'tabs'      => ['部门'],
        ],
        '学校管理员'    => [
            'id'        => 0,
            'parent_id' => '系统设置',
            'uri'       => null,
            'icon'      => 'fa fa-user-plus',
            'tabs'      => ['超级用户'],
        ],
        '角色/权限'    => [
            'id'        => 0,
            'parent_id' => '系统设置',
            'uri'       => null,
            'icon'      => 'fa fa-meh-o',
            'tabs'      => ['角色'],
        ],
        '学校设置'     => [
            'id'        => 0,
            'parent_id' => '系统设置',
            'uri'       => 'schools/edit/',
            'icon'      => 'fa fa-university',
            'tabs'      => null,
        ],
        '年级/班级'    => [
            'id'        => 0,
            'parent_id' => '系统设置',
            'uri'       => null,
            'icon'      => 'fa fa-users',
            'tabs'      => ['年级', '班级'],
        ],
        '科目设置'     => [
            'id'        => 0,
            'parent_id' => '系统设置',
            'uri'       => null,
            'icon'      => 'fa fa-book',
            'tabs'      => ['科目', '科目次分类'],
        ],
        '学期设置'     => [
            'id'        => 0,
            'parent_id' => '系统设置',
            'uri'       => null,
            'icon'      => null,
            'tabs'      => ['学期'],
        ],
        '套餐设置'     => [
            'id'        => 0,
            'parent_id' => '系统设置',
            'uri'       => null,
            'icon'      => null,
            'tabs'      => ['套餐类型'],
        ],
    ];
    
    protected $educatorMenus = [
        '首页', '消息中心', '通讯录', '学生', '教职员工', '监护人', '标签',
        '考勤管理', '学生考勤查询统计', '学生消费管理', '成绩中心', '考试设置',
        '成绩管理', '用户中心', '个人信息', '重置密码', '我的消息', '待办事项',
    ];
    
    protected $educatorTabs = [
        '消息中心', '学生', '班级', '教职员工', '监护人', '学生考勤记录',
        '消费记录', '考试', '成绩', '成绩统计项', '总成绩', '标签管理',
    ];
    
    protected $school, $userId;
    
    /**
     * Create ent job instncent   *
     * @param School $school
     * @param $userId
     */
    public function __construct(School $school, $userId) {
        
        $this->school = $school;
        $this->userId = $userId;
        
    }
    
    /**
     * Exeute the job.
     *
     * @throws Throwable
     */
    public function handle() {
        
        $response = [
            'userId'     => $this->userId,
            'title'      => '创建学校',
            'statusCode' => HttpStatusCode::OK,
            'message'    => __('messages.school.menu_created'),
        ];
        try {
            DB::transaction(function () {
                $position = Menu::all()->max('position');
                # 创建学校微网站
                WapSite::create([
                    'school_id'  => $this->school->id,
                    'site_title' => $this->school->name,
                    'media_ids'  => '0',
                    'enabled'    => Constant::ENABLED,
                ]);
                # 创建学校基础菜单
                $menuTypeId = MenuType::whereName('其他')->first()->id;
                foreach ($this->menus as $name => &$data) {
                    if ($data['uri'] == 'schools/edit/') {
                        $data['uri'] .= $this->school->id;
                    }
                    # 创建
                    $menu = Menu::create([
                        'parent_id'    => !$data['parent_id']
                            ? $this->school->menu_id
                            : $this->menus[$data['parent_id']]['id'],
                        'menu_type_id' => $menuTypeId,
                        'name'         => $name,
                        'uri'          => $data['uri'],
                        'position'     => $position += 1,
                        'icon_id'      => $data['icon'] ? Icon::whereName($data['icon'])->first()->id : null,
                        'enabled'      => Constant::ENABLED,
                    ]);
                    $data['id'] = $menu->id;
                    # 创建菜单卡片绑定关系
                    if ($data['tabs']) {
                        foreach ($data['tabs'] as $name) {
                            $tab = Tab::whereComment($name)->first();
                            MenuTab::create([
                                'menu_id' => $menu->id,
                                'tab_id'  => $tab->id,
                                'enabled' => Constant::ENABLED,
                            ]);
                        }
                    }
                }
                # 创建“教职员工”基本角色
                $menuIds = $tabIds = $actionIds = [];
                foreach ($this->educatorMenus as $name) {
                    $menuIds[] = $this->menus[$name]['id'];
                }
                $tabIds = Tab::whereIn('comment', $this->educatorTabs)->pluck('id')->toArray();
                $actionIds = Action::whereIn('tab_id', $tabIds)->pluck('id')->toArray();
                (new Group)->store([
                    'name'       => '教职员工',
                    'remark'     => '基本角色',
                    'school_id'  => $this->school->id,
                    'enabled'    => Constant::ENABLED,
                    'menu_ids'   => $menuIds,
                    'tab_ids'    => $tabIds,
                    'action_ids' => $actionIds,
                ]);
            });
        } catch (Exception $e) {
            $response['statusCode'] = HttpStatusCode::INTERNAL_SERVER_ERROR;
            $response['message'] = $e->getMessage();
        }
        if ($this->userId) {
            event(new JobResponse($response));
        }
        
    }
    
}
