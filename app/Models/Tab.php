<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait, Snippet};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasMany};
use Illuminate\Support\Facades\{Auth, DB, Request};
use ReflectionClass;
use Throwable;

/**
 * App\Models\Tab 控制器
 *
 * @property int $id
 * @property string $name 卡片名称
 * @property int $group_id 所属角色Id
 * @property int $action_id 默认加载的Action ID
 * @property int|null $icon_id 图标ID
 * @property string $comment 控制器(中文)名称
 * @property string|null $remark 卡片备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $category 控制器类型: 0 - 后台，1 - 微信端, 2 - 其他
 * @property-read Icon|null $icon
 * @property-read Collection|Menu[] $menus
 * @property-read Collection|Action[] $actions
 * @method static Builder|Tab whereActionId($value)
 * @method static Builder|Tab whereComment($value)
 * @method static Builder|Tab whereCreatedAt($value)
 * @method static Builder|Tab whereEnabled($value)
 * @method static Builder|Tab whereGroupId($value)
 * @method static Builder|Tab whereIconId($value)
 * @method static Builder|Tab whereId($value)
 * @method static Builder|Tab whereName($value)
 * @method static Builder|Tab whereRemark($value)
 * @method static Builder|Tab whereUpdatedAt($value)
 * @method static Builder|Tab whereCategory($value)
 * @method static Builder|Tab newModelQuery()
 * @method static Builder|Tab newQuery()
 * @method static Builder|Tab query()
 * @mixin Eloquent
 * @property-read Collection|Group[] $groups
 */
class Tab extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'comment', 'icon_id', 'group_id',
        'action_id', 'remark', 'category', 'enabled',
    ];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /**
     * 返回指定卡片所属的菜单对象
     *
     * @return BelongsToMany
     */
    function menus() { return $this->belongsToMany('App\Models\Menu', 'menu_tab'); }
    
    /**
     * 返回指定卡片所属的图标对象
     *
     * @return BelongsTo
     */
    function icon() { return $this->belongsTo('App\Models\Icon'); }
    
    /**
     * 返回指定卡片默认的Action对象
     *
     * @return HasMany
     */
    function actions() { return $this->hasMany('App\Models\Action'); }
    
    /**
     * 返回指定卡片所属的角色对象
     *
     * @return BelongsToMany
     */
    function groups() { return $this->belongsToMany('App\Models\Group', 'group_tab'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
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
                    $iconId = $this->find($row['id'])->icon_id;
                    
                    return sprintf(
                            Snippet::ICON,
                            $iconId ? Icon::find($iconId)->name : 'fa-calendar-check-o text-gray',
                            ''
                        ) . $d;
                },
            ],
            ['db' => 'Tab.comment', 'dt' => 2],
            [
                'db'        => 'Tab.group_id', 'dt' => 3,
                'formatter' => function ($d) {
                    $group = $d ? Group::find($d)->name : '所有';
                    $colors = [
                        '所有' => 'text-black',
                        '运营' => 'text-light-blue',
                        '企业' => 'text-green',
                        '学校' => 'text-fuchsia',
                    ];
                    
                    return sprintf(Snippet::BADGE, $colors[$group], $group);
                },
            ],
            [
                'db'        => 'Action.name as actionname', 'dt' => 4,
                'formatter' => function ($d) {
                    return !empty($d)
                        ? sprintf(Snippet::ICON, 'fa-gears', '') . $d
                        : '-';
                },
            ],
            ['db' => 'Tab.created_at', 'dt' => 5],
            ['db' => 'Tab.updated_at', 'dt' => 6],
            [
                'db'        => 'Tab.category', 'dt' => 7,
                'formatter' => function ($d) {
                    $category = !$d ? '后台' : ($d == 1 ? '前端' : '其他');
                    $colors = [
                        '后台' => 'text-light-blue',
                        '前端' => 'text-green',
                        '其他' => 'text-gray',
                    ];
                    
                    return sprintf(Snippet::BADGE, $colors[$category], $category);
                },
            ],
            [
                'db'        => 'Tab.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false, true, false);
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
            $this, $columns, $joins
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
                (new MenuTab)->store($tab->id, $data['menu_ids'], false);
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
            if (!($tab = $this->find($id))) return false;
            try {
                DB::transaction(function () use ($data, $id, $tab) {
                    $tab->update($data);
                    (new MenuTab)->store($id, $data['menu_ids'], false);
                });
            } catch (Exception $e) {
                throw $e;
            }
        } else {
            $ids = Request::input('ids');
            $action = Request::input('action');
            
            return $this->whereIn('id', $ids)->update([
                'enabled' => $action == 'enable' ? 1 : 0,
            ]);
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
    function remove($id = null) {
        
        return $this->purge(['Tab', 'MenuTab'], 'tab_id', 'purge', $id);
        
    }
    
    /**
     * 扫描
     *
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    function scan() {
        
        try {
            DB::transaction(function () {
                $action = new Action();
                $controllers = $this->controllerPaths($action->siteRoot() . Constant::CONTROLLER_DIR);
                $action->controllerNamespaces($controllers);
                // remove nonexisting controllers
                $ctlrs = array_merge(
                    array_diff(
                        $this->pluck('name')->toArray(),
                        $action->controllerNames($controllers)
                    ),
                    Constant::EXCLUDED_CONTROLLERS
                );
                foreach ($ctlrs as $ctlr) {
                    $tab = $this->whereName($ctlr)->first();
                    throw_if(
                        $tab && !$this->remove($tab->id),
                        new Exception(__('messages.del_fail'))
                    );
                }
                // create new Tabs or update the existing ones
                foreach ($controllers as $controller) {
                    $obj = new ReflectionClass(ucfirst($controller));
                    $ctlrNameSpace = $obj->getName();
                    $paths = explode('\\', $ctlrNameSpace);
                    $ctlrName = $paths[sizeof($paths) - 1];
                    if (in_array($ctlrName, Constant::EXCLUDED_CONTROLLERS)) continue;
                    $record = [
                        'name'      => $ctlrName,
                        'comment'   => self::controllerComments($obj),
                        'remark'    => $controller,
                        'action_id' => self::indexActionId($ctlrName),
                        'category'  => $obj->hasProperty('category')
                            ? $obj->getProperty('category')->getValue() : 0,
                        'enabled'   => Constant::ENABLED,
                    ];
                    $tab = $this->whereName($record['name'])->first();
                    if ($tab) {
                        $tab->comment = $record['comment'];
                        $tab->category = $record['category'];
                        if (empty($tab->action_id)) {
                            $tab->action_id = $record['action_id'];
                        }
                        $tab->save();
                    } else {
                        $this->create($record);
                    }
                }
                unset($action);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */

    function compose() {
    
        $action = explode('/', Request::path())[1];
        $roles = Group::whereIn('name', ['运营', '企业', '学校'])->pluck('name', 'id');
        if ($action == 'index') {
            $nil = collect([null => '全部']);
            $data = [
                'batch'  => true, # 需要批量操作
                'titles' => [
                    '#', '控制器', '名称',
                    [
                        'title' => '角色',
                        'html'  => $this->htmlSelect(
                            $nil->union($roles), 'filter_group'
                        ),
                    ],
                    '默认功能',
                    ['title' => '创建于', 'html'  => $this->htmlDTRange('创建于')],
                    ['title' => '更新于', 'html'  => $this->htmlDTRange('更新于')],
                    [
                        'title' => '类型',
                        'html'  => $this->htmlSelect(
                            $nil->union(['后台', '前端', '其他']), 'filter_category'
                        ),
                    ],
                    [
                        'title' => '状态 . 操作',
                        'html'  => $this->htmlSelect(
                            $nil->union(['已禁用', '已启用']), 'filter_enabled'
                        ),
                    ],
                ],
                'filter' => true,
            ];
        } else {
            $tab = Tab::find(Request::route('id'));
            $tabMenus = $tab ? $tab->menus : null;
            $data = [
                'icons'         => (new Icon)->icons(),
                'actions'       => (new Action)->actions(),
                'groups'        => collect([0 => '所有'])->union($roles),
                'menus'         => (new Menu)->leaves(1),
                'selectedMenus' => $tabMenus ? $tabMenus->pluck('id') : collect([]),
            ];
        }
    
        return $data;
        
    }

    /**
     * 根据角色返回可访问的卡片id
     *
     * @return array
     */
    function allowedTabIds() {
        
        $user = Auth::user();
        $role = $user->role();
        list($corpGid, $schoolGid) = array_map(
            function ($role) {
                return Group::whereName($role)->first()->id;
            }, ['企业', '学校']
        );
        switch ($role) {
            case '运营':
                $builder = $this->whereEnabled(1);
                break;
            case '企业':
                $builder = $this->whereEnabled(1)
                    ->whereIn('group_id', [0, $corpGid, $schoolGid]);
                break;
            case '学校':
                $builder = $this->whereEnabled(1)
                    ->whereIn('group_id', [0, $schoolGid]);
                break;
            default:
                $builder = GroupTab::whereGroupId($user->group_id);
                $field = 'tab_id';
                break;
        }
        
        return $builder->pluck($field ?? 'id')->toArray();
        
    }

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
     * 获取index方法的action_id
     *
     * @param $ctlrName
     * @return int|mixed
     */
    private function indexActionId($ctlrName) {
        
        $tab = Tab::whereName($ctlrName)->first();
        $action = (new Action)->where([
            'enabled' => 1,
            'tab_id'  => $tab ? $tab->id : 0,
            'method'  => 'index',
        ])->first();
        
        return $action ? $action->id : 0;
        
    }
    
}
