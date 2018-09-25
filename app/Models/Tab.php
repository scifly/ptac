<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
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
use Illuminate\Support\Facades\Request;
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
 * @property int $category 控制器类型: 0 - 后台，1 - 微信端, 2 - 其他
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
 * @method static Builder|Tab whereCategory($value)
 * @mixin Eloquent
 * @property int|null $new_column
 * @method static Builder|Tab whereNewColumn($value)
 */
class Tab extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'controller', 'icon_id', 'group_id',
        'action_id', 'remark', 'category', 'enabled',
    ];
    
    /**
     * 返回指定卡片所属的菜单对象
     *
     * @return BelongsToMany
     */
    function menus() { return $this->belongsToMany('App\Models\Menu', 'menus_tabs'); }
    
    /**
     * 返回指定卡片所属的图标对象
     *
     * @return BelongsTo
     */
    function icon() { return $this->belongsTo('App\Models\Icon'); }
    
    /**
     * 返回指定卡片默认的Action对象
     *
     * @return BelongsTo
     */
    function action() { return $this->belongsTo('App\Models\Action'); }
    
    /**
     * 卡片列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Tab.id', 'dt' => 0],
            [
                'db'        => 'Tab.name', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    $iconId = self::find($row['id'])->icon_id;
                    if ($iconId) {
                        return sprintf(Snippet::ICON, Icon::find($iconId)->name, '') . $d;
                    }
                    
                    return sprintf(Snippet::ICON, 'fa-calendar-check-o text-gray', '') . $d;
                },
            ],
            ['db' => 'Tab.controller', 'dt' => 2],
            [
                'db'        => 'Tab.group_id', 'dt' => 3,
                'formatter' => function ($d) {
                    $badge = Snippet::BADGE_BLACK;
                    if ($d == 0) {
                        return sprintf($badge, '所有');
                    }
                    $group = Group::find($d)->name;
                    switch ($group) {
                        case '运营':
                            $badge = Snippet::BADGE_LIGHT_BLUE;
                            break;
                        case '企业':
                            $badge = Snippet::BADGE_GREEN;
                            break;
                        case '学校':
                            $badge = Snippet::BADGE_FUCHSIA;
                            break;
                        default:
                            break;
                        
                    }
                    
                    return sprintf($badge, $group);
                },
            ],
            [
                'db'        => 'Action.name as actionname', 'dt' => 4,
                'formatter' => function ($d) {
                    return !empty($d)
                        ? sprintf(Snippet::ICON, 'fa-gears', '') . $d
                        : $d;
                },
            ],
            ['db' => 'Tab.created_at', 'dt' => 5],
            ['db' => 'Tab.updated_at', 'dt' => 6],
            [
                'db'        => 'Tab.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    $id = $row['id'];
                    $editLink = sprintf(Snippet::DT_LINK_EDIT, 'edit_' . $id);
                    
                    return Snippet::status($d) . $editLink;
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'actions',
                'alias'      => 'Action',
                'type'       => 'LEFT',
                'conditions' => [
                    'Action.id = Tab.action_id',
                ],
            ],
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins
        );
        
    }
    
    /**
     * 保存卡片
     *
     * @param array $data
     * @return bool|mixed
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                $tab = $this->create($data);
                (new MenuTab)->storeByTabId($tab->id, $data['menu_ids']);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
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
    function modify(array $data, $id = null) {
        
        if (isset($id)) {
            $tab = self::find($id);
            if (!isset($tab)) {
                return false;
            }
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
        } else {
            $ids = Request::input('ids');
            $action = Request::input('action');
            
            return $this->whereIn('id', $ids)->update([
                'enabled' => $action == 'enable' ? Constant::ENABLED : Constant::DISABLED,
            ]);
        }
        
        return true;
        
    }
    
    /**
     * 删除指定卡片的所有数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                MenuTab::whereTabId($id)->delete();
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 扫描
     *
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    function scan() {
        
        $action = new Action();
        $controllers = self::controllerPaths($action->siteRoot() . Constant::CONTROLLER_DIR);
        $action->controllerNamespaces($controllers);
        $controllerNames = $action->controllerNames($controllers);
        // remove nonexisting controllers
        $existingCtlrs = [];
        $ctlrs = self::groupBy('controller')->get(['controller'])->toArray();
        foreach ($ctlrs as $ctlr) {
            $existingCtlrs[] = $ctlr['controller'];
        }
        $ctlrDiff = array_merge(
            array_diff($existingCtlrs, $controllerNames),
            Constant::EXCLUDED_CONTROLLERS
        );
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
            if (in_array($ctlrName, Constant::EXCLUDED_CONTROLLERS)) continue;
            $record = [
                'name'       => self::controllerComments($obj),
                'controller' => $ctlrName,
                'remark'     => $controller,
                'action_id'  => self::indexActionId($ctlrName),
                'category'   => $obj->hasProperty('category') ? $obj->getProperty('category')->getValue() : 0,
                'enabled'    => Constant::ENABLED,
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
     * 返回所有控制器的完整路径
     *
     * @param $rootDir
     * @param array $allData
     * @return array
     */
    private function controllerPaths($rootDir, $allData = []) {
        
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
    
    /**
     * 移除指定的卡片
     *
     * @param $id
     * @return bool|mixed
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */

    /**
     * 获取指定控制器的注释文本
     *
     * @param ReflectionClass $controller
     * @return mixed|string
     */
    private function controllerComments(ReflectionClass $controller) {
        
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
    private function indexActionId($ctlrName) {
        
        $action = (new Action)->where([
            'enabled' => 1,
            'controller' => $ctlrName,
            'method' => 'index'
        ])->first();
        
        return $action ? $action->id : 0;
        
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
                return self::whereEnabled(Constant::ENABLED)
                    ->pluck('id')
                    ->toArray();
            case '企业':
                return self::whereEnabled(Constant::ENABLED)
                    ->whereIn('group_id', [Constant::SHARED, Constant::CORP, Constant::SCHOOL])
                    ->pluck('id')
                    ->toArray();
            case '学校':
                return self::whereEnabled(Constant::ENABLED)
                    ->whereIn('group_id', [Constant::SHARED, Constant::SCHOOL])
                    ->pluck('id')
                    ->toArray();
            default:
                return GroupTab::whereGroupId($user->group_id)
                    ->pluck('tab_id')
                    ->toArray();
        }
        
    }
    
}
