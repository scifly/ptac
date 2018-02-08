<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use Throwable;

/**
 * App\Models\Tab 卡片
 *
 * @property int $id
 * @property string $name 卡片名称
 * @property int $group_id 所属角色Id
 * @property int $action_id 默认加载的Action ID
 * @property int|null $icon_id 图标ID
 * @property string $controller 控制器名称
 * @property string|null $remark 卡片备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Action $action
 * @property-read Icon|null $icon
 * @property-read Collection|Menu[] $menus
 * @method static Builder|Tab whereActionId($value)
 * @method static Builder|Tab whereController($value)
 * @method static Builder|Tab whereCreatedAt($value)
 * @method static Builder|Tab whereEnabled($value)
 * @method static Builder|Tab whereGroupId($value)
 * @method static Builder|Tab whereIconId($value)
 * @method static Builder|Tab whereId($value)
 * @method static Builder|Tab whereName($value)
 * @method static Builder|Tab whereRemark($value)
 * @method static Builder|Tab whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int|null $new_column
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tab whereNewColumn($value)
 */
class Tab extends Model {

    protected $fillable = [
        'name', 'remark', 'icon_id','group_id',
        'action_id', 'enabled', 'controller',
    ];
    const EXCLUDED_CONTROLLERS = [
        'ForgotPasswordController', 'Controller', 'RegisterController',
        'LoginController', 'ResetPasswordController', 'TestController',
        'MessageCenterController', 'HomeWorkController', 'MobileSiteController'
    ];
    const CONTROLLER_DIR = 'app/Http/Controllers';

    /**
     * 返回指定卡片所属的菜单对象
     *
     * @return BelongsToMany
     */
    public function menus() { return $this->belongsToMany('App\Models\Menu', 'menus_tabs'); }

    /**
     * 返回指定卡片所属的图标对象
     *
     * @return BelongsTo
     */
    public function icon() { return $this->belongsTo('App\Models\Icon'); }

    /**
     * 返回指定卡片默认的Action对象
     *
     * @return BelongsTo
     */
    public function action() { return $this->belongsTo('App\Models\Action'); }
    
    /**
     * 扫描
     *
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    static function scan() {

        $action = new Action();
        $controllers = self::controllerPaths($action->getSiteRoot() . self::CONTROLLER_DIR);
        $action->getControllerNamespaces($controllers);
        $controllerNames = $action->getControllerNames($controllers);
        // remove nonexisting controllers
        $existingCtlrs = [];
        $ctlrs = self::groupBy('controller')->get(['controller'])->toArray();
        foreach ($ctlrs as $ctlr) {
            $existingCtlrs[] = $ctlr['controller'];
        }
        $ctlrDiff = array_diff($existingCtlrs, $controllerNames);
        foreach ($ctlrDiff as $ctlr) {
            $tab = self::whereController($ctlr)->first();
            if ($tab && !self::remove($tab->id)) {
                return false;
            }
        }
        // create new Tabs or update the existing Tabs
        foreach ($controllers as $controller) {
            $obj = new ReflectionClass(ucfirst($controller));
            $ctlrNameSpace = $obj->getName();
            $paths = explode('\\', $ctlrNameSpace);
            $ctlrName = $paths[sizeof($paths) - 1];
            if (in_array($ctlrName, self::EXCLUDED_CONTROLLERS)) continue;
            $record = [
                'name' => self::controllerComments($obj),
                'controller' => $ctlrName,
                'remark' => $controller,
                'action_id' => self::indexActionId($ctlrName),
                'enabled' => 1,
            ];
            $tab = self::whereController($record['controller'])->first();
            if ($tab) {
                $tab->name = $record['name'];
                if (empty($tab->action_id)) {
                    $tab->action_id = $record['action_id'];
                }
                $tab->save();
            } else {
                self::create($record);
            }
        }
        unset($action);
        
        return true;

    }
    
    /**
     * 保存卡片
     *
     * @param array $data
     * @return bool|mixed
     * @throws Throwable
     */
    static function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                $t = self::create($data);
                $menuTab = new MenuTab();
                $menuIds = $data['menu_ids'];
                $menuTab->storeByTabId($t->id, $menuIds);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 移除指定的卡片
     *
     * @param $id
     * @return bool|mixed
     * @throws Throwable
     */
    static function remove($id) {

        $tab = self::find($id);
        if (!isset($tab)) { return false; }
        try {
            DB::transaction(function () use ($id, $tab) {
                # 删除指定的卡片记录
                $tab->delete();
                # 删除与指定卡片绑定的菜单记录
                MenuTab::whereTabId($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;

    }
    
    /**
     * 卡片列表
     *
     * @return array
     */
    static function datatable() {

        $columns = [
            ['db' => 'Tab.id', 'dt' => 0],
            [
                'db' => 'Tab.name', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    $iconId = self::find($row['id'])->icon_id;
                    if ($iconId) {
                        return '<i class="' . Icon::find($iconId)->name . '"></i>&nbsp;' . $d;
                    }
                    return '<i class="fa fa-calendar-check-o"></i>&nbsp;' . $d;
                }
            ],
            [
                'db' => 'Action.name as actionname', 'dt' => 2,
                'formatter' => function ($d) {
                    return '<i class="fa fa-gears"></i>&nbsp;' . $d;
                }
            ],
            ['db' => 'Tab.created_at', 'dt' => 3],
            ['db' => 'Tab.updated_at', 'dt' => 4],
            [
                'db' => 'Tab.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    $id = $row['id'];
                    $status = $d ? Datatable::DT_ON : Datatable::DT_OFF;
                    $editLink = sprintf(Datatable::DT_LINK_EDIT, 'edit_' . $id);
                    return
                        $status . str_repeat('&nbsp;', 3) .
                        $editLink;
                },
            ],
        ];
        $joins = [
            [
                'table' => 'actions',
                'alias' => 'Action',
                'type' => 'LEFT',
                'conditions' => [
                    'Action.id = Tab.action_id',
                ],
            ],
        ];
        
        return Datatable::simple(self::getModel(), $columns, $joins);

    }
    
    /**
     * 更新指定的卡片
     *
     * @param array $data
     * @param $id
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    static function modify(array $data, $id) {

        $tab = self::find($id);
        if (!isset($tab)) { return false; }
        try {
            DB::transaction(function () use ($data, $id, $tab) {
                $tab->update($data);
                $menuIds = $data['menu_ids'];
                $menuTab = new MenuTab();
                $menuTab::whereTabId($id)->delete();
                $menuTab->storeByTabId($id, $menuIds);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;

    }
    
    /**
     * 根据角色返回可访问的卡片ids
     *
     * @return array
     */
    function allowedTabIds() {
    
        $user = Auth::user();
        $role = $user->group->name;
        switch ($role) {
            case '运营':
                return self::whereEnabled(1)
                    ->pluck('id')
                    ->toArray();
            case '企业':
                return self::whereEnabled(1)
                    ->whereIn('group_id', [0, 2, 3])
                    ->pluck('id')
                    ->toArray();
            case '学校':
                return self::whereEnabled(1)
                    ->whereIn('group_id', [0, 3])
                    ->pluck('id')
                    ->toArray();

//            case '教职员工':
//                return self::whereEnabled(1)
//                    ->whereIn('group_id', [0, 3])
//                    ->pluck('id')
//                    ->toArray();
            default:
                return GroupTab::whereGroupId($user->group_id)
                    ->pluck('tab_id')
                    ->toArray();
        }
        
    }
    
    /**
     * 获取指定控制器的注释文本
     *
     * @param ReflectionClass $controller
     * @return mixed|string
     */
    private static function controllerComments(ReflectionClass $controller) {
        
        $comment = $controller->getDocComment();
        $name = 'n/a';
        preg_match_all("#\/\*\*\n\s{1}\*[^\*]*\*#", $comment, $matches);
        if (isset($matches[0][0])) {
            $name = str_replace(str_split("\r\n/* "), '', $matches[0][0]);
        } else {
            preg_match_all("#\/\*\*\r\n\s{1}\*[^\*]*\*#", $comment, $matches);
            if (isset($matches[0][0])) {
                $name = str_replace(str_split("\r\n/* "), '', $matches[0][0]);
            }
        }
        
        return $name;
        
    }
    
    /**
     * 获取index方法的action_id
     *
     * @param $ctlrName
     * @return int|mixed
     */
    private static function indexActionId($ctlrName) {
        
        $action = new Action();
        $a = $actionId = $action::whereEnabled(1)
            ->where('controller', $ctlrName)
            ->where('method', 'index')
            ->first();
        if (!$a) {
            return 0;
        }
        
        return $a->id;
        
    }
    
    /**
     * 返回所有控制器的完整路径
     *
     * @param $rootDir
     * @param array $allData
     * @return array
     */
    private static function controllerPaths($rootDir, $allData = []) {
        
        // set filenames invisible if you want
        $invisibleFileNames = [".", "..", ".htaccess", ".htpasswd"];
        // run through content of root directory
        $dirContent = scandir($rootDir);
        foreach ($dirContent as $key => $content) {
            // filter all files not accessible
            $path = $rootDir . '/' . $content;
            if (!in_array($content, $invisibleFileNames)) {
                // if content is file & readable, add to array
                if (is_file($path) && is_readable($path)) {
                    // save file name with path
                    $allData[] = $path;
                    // if content is a directory and readable, add path and name
                } elseif (is_dir($path) && is_readable($path)) {
                    // recursive callback to open new directory
                    $allData = self::controllerPaths($path, $allData);
                }
            }
        }
        
        return $allData;
        
    }

}
