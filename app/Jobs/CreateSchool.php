<?php
namespace App\Jobs;

use App\Helpers\{Broadcaster, Constant, JobTrait};
use App\Models\{Action, Group, Icon, Menu, MenuTab, MenuType, School, Tab, WapSite};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
    Support\Facades\DB};
use Pusher\PusherException;
use Throwable;

/**
 * Class CreateSchoolMenu
 * @package App\Jobs
 */
class CreateSchool implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobTrait;
    
    protected $parentMenu, $menuNameTab, $menu, $menuTab;
    
    protected $menus = [
        '首页' => [0, null, 'home', 'fa fa-home', []],
        '消息中心' => [0, null, 'messages/index', 'fa fa-send-o', null],
        '通讯录' => [0, null, null, 'fa fa-book', null],
        '学生' => [0, '通讯录', null, 'fa fa-child', ['学生', '班级']],
        '教职员工' => [0, '通讯录', null, 'fa fa-user', ['教职员工']],
        '监护人' => [0, '通讯录', null, 'fa fa-user', ['监护人', '学生']],
        '标签' => [0, '通讯录', null, 'fa fa-tags', ['标签管理']],
        '一卡通管理' => [0, '通讯录', null, 'fa fa-tags', ['一卡通']],
        '人脸识别管理' => [0, '通讯录', null, 'fa fa-tags', ['人脸识别', '人脸识别设备']],
        '考勤管理' => [0, null, null, 'fa fa-credit-card', null],
        '门禁设置' => [0, '考勤管理', null, 'fa fa-credit-card', ['门禁通行规则', '门禁设备']],
        '通行记录' => [0, '考勤管理', null, 'fa fa-commenting', ['门禁通行记录']],
        '学生消费管理' => [0, null, null, 'fa fa-money', ['消费记录']],
        '成绩中心' => [0, null, null, 'fa fa-file', null],
        '考试设置' => [0, '成绩中心', null, null, ['考试', '考试类型']],
        '成绩管理' => [0, '成绩中心', null, null, ['成绩', '成绩统计项', '总成绩']],
        '自媒体管理' => [0, null, null, 'fa fa-photo', ['微网站栏目', '微网站文章', '微网站']],
        '用户中心' => [0, null, null, 'fa fa-user', null],
        '个人信息' => [0, '用户中心', 'users/edit', 'fa fa-user', null],
        '重置密码' => [0, '用户中心', 'users/reset', 'fa fa-user', null],
        '我的消息' => [0, '用户中心', 'users/message', 'fa fa-user', null],
        '待办事项' => [0, '用户中心', 'users/event', 'fa fa-user', null],
        '系统设置' => [0, null, null, 'fa fa-gear', null],
        '菜单管理' => [0, '系统设置', null, 'fa fa-list-ul', ['菜单']],
        '部门管理' => [0, '系统设置', null, 'fa fa-sitemap', ['部门']],
        '学校管理员' => [0, '系统设置', null, 'fa fa-user-plus', ['超级用户']],
        '角色/权限' => [0, '系统设置', null, 'fa fa-meh-o', ['角色']],
        '年级/班级' => [0, '系统设置', null, 'fa fa-users', ['年级', '班级']],
        '科目设置' => [0, '系统设置', null, 'fa fa-book', ['科目', '科目次分类']],
        '学期设置' => [0, '系统设置', null, null, ['学期']],
        '套餐设置' => [0, '系统设置', null, null, ['套餐类型']],
    ];
    
    protected $educatorMenus = [
        '首页', '消息中心', '通讯录', '学生', '教职员工', '监护人',
        '考勤管理', '通行记录', '学生消费管理', '成绩中心', '考试设置',
        '成绩管理', '用户中心', '个人信息', '重置密码', '我的消息', '待办事项',
    ];
    
    protected $educatorTabs = [
        '消息中心', '学生', '班级', '教职员工', '监护人', '门禁通行记录',
        '消费记录', '考试', '成绩', '成绩统计项', '总成绩',
    ];
    
    protected $school, $userId, $response;
    
    protected $broadcaster;
    
    /**
     * Create ent job instncent   *
     * @param School $school
     * @param $userId
     * @throws PusherException
     */
    function __construct(School $school, $userId) {
        
        $this->school = $school;
        $this->userId = $userId;
        $this->broadcaster = new Broadcaster();
        $this->response = [
            'userId'     => $this->userId,
            'title'      => '创建学校',
            'statusCode' => Constant::OK,
            'message'    => __('messages.school.menu_created'),
        ];
        
    }
    
    /**
     * Execute the job
     *
     * @return bool
     * @throws PusherException
     * @throws Throwable
     */
    function handle() {
        
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
                    [$id, $parent, $uri, $icon, $tabs] = $data;
                    # 创建菜单
                    $menuId = Menu::insertGetId([
                        'parent_id'    => !$parent
                            ? $this->school->menu_id
                            : $this->menus[$parent][$id],
                        'menu_type_id' => $menuTypeId,
                        'name'         => $name,
                        'uri'          => $uri,
                        'position'     => $position += 1,
                        'icon_id'      => $icon ? Icon::whereName($icon)->first()->id : null,
                        'enabled'      => Constant::ENABLED,
                    ]);
                    $data[$id] = $menuId;
                    # 创建菜单卡片绑定关系
                    foreach ($tabs ?? [] as $name) {
                        $tab = Tab::whereComment($name)->first();
                        MenuTab::create([
                            'menu_id' => $menuId,
                            'tab_id'  => $tab->id,
                            'enabled' => Constant::ENABLED,
                        ]);
                    }
                }
                # 创建“教职员工”基本角色
                foreach ($this->educatorMenus as $name) {
                    $menuIds[] = $this->menus[$name][0];
                }
                $tabIds = Tab::whereIn('comment', $this->educatorTabs)->pluck('id');
                $actionIds = Action::whereIn('tab_id', $tabIds)->pluck('id');
                (new Group)->store([
                    'name'       => '教职员工',
                    'remark'     => '基本角色',
                    'school_id'  => $this->school->id,
                    'enabled'    => Constant::ENABLED,
                    'menu_ids'   => $menuIds ?? [],
                    'tab_ids'    => $tabIds->toArray(),
                    'action_ids' => $actionIds->toArray(),
                ]);
            });
        } catch (Exception $e) {
            $this->eHandler($this, $e);
        }
        !$this->userId ?: $this->broadcaster->broadcast($this->response);
        
        return true;
        
    }
    
    /**
     * 任务异常处理
     *
     * @param Exception $e
     * @throws Exception
     */
    function failed(Exception $e) {
        
        $this->eHandler($this, $e);
        
    }
    
}