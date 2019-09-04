<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait, Snippet};
use App\Models\ActionType as ActionType;
use Carbon\Carbon;
use Doctrine\Common\Inflector\Inflector;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\BelongsToMany};
use Illuminate\Support\{Facades\DB, Facades\Request, Facades\Route, Str};
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;

/**
 * App\Models\Action 功能
 *
 * @property int $id
 * @property string $name method/function名称
 * @property string|null $action_type_ids HTTP请求类型IDs
 * @property string|null $remark 备注
 * @property string $controller 所属controller类名
 * @property string $tab_id 所属控制器id
 * @property string|null $view 对应的blade view名
 * @property string $method
 * @property string|null $js
 * @property string|null $route
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Tab $tab
 * @property-read Collection|Group[] $groups
 * @method static Builder|Action whereActionTypeIds($value)
 * @method static Builder|Action whereController($value)
 * @method static Builder|Action whereTabId($value)
 * @method static Builder|Action whereCreatedAt($value)
 * @method static Builder|Action whereEnabled($value)
 * @method static Builder|Action whereId($value)
 * @method static Builder|Action whereJs($value)
 * @method static Builder|Action whereMethod($value)
 * @method static Builder|Action whereName($value)
 * @method static Builder|Action whereRemark($value)
 * @method static Builder|Action whereRoute($value)
 * @method static Builder|Action whereUpdatedAt($value)
 * @method static Builder|Action whereView($value)
 * @method static Builder|Action newModelQuery()
 * @method static Builder|Action newQuery()
 * @method static Builder|Action query()
 * @mixin Eloquent
 */
class Action extends Model {
    
    use ModelTrait;
    
    const ACTIONS_WITHOUT_VIEW_AND_JS = [
        'destroy', 'store', 'update', 'sync', 'export',
        'move', 'rankTabs', 'sanction', 'import', 'rank',
        'detail', 'studentConsumption', 'sendMsg',
    ];
    public $type = 1;
    protected $fillable = [
        'name', 'method', 'remark',
        'tab_id', 'view', 'route',
        'js', 'action_type_ids', 'enabled',
    ];
    protected $routes;
    protected $acronyms;
    protected $actionTypes;
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /**
     * 返回当前action包含的卡片
     *
     * @return BelongsTo
     */
    function tab() { return $this->belongsTo('App\Models\Tab'); }
    
    /**
     * 返回当前action所属的角色对象collection
     *
     * @return BelongsToMany
     */
    function groups() { return $this->belongsToMany('App\Models\Group', 'action_group'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * 功能列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Action.id', 'dt' => 0],
            [
                'db'        => 'Action.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return empty($d) ? '-' : sprintf(Snippet::ICON, 'fa fa-gears', '') . $d;
                },
            ],
            [
                'db'        => 'Action.method', 'dt' => 2,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Snippet::BADGE, 'text-green', $d) : '-';
                },
            ],
            [
                'db'        => 'Action.route', 'dt' => 3,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Snippet::BADGE, 'text-yellow', $d) : '-';
                },
            ],
            [
                'db'        => 'Tab.name as controller', 'dt' => 4,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Snippet::BADGE, 'text-red', $d) : '-';
                },
            ],
            [
                'db'        => 'Action.view', 'dt' => 5,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Snippet::BADGE, 'text-light-blue', $d) : '-';
                },
            ],
            [
                'db'        => 'Action.js', 'dt' => 6,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Snippet::BADGE, 'text-maroon', $d) : '-';
                },
            ],
            [
                'db'        => 'Action.action_type_ids', 'dt' => 7,
                'formatter' => function ($d) {
                    return !empty($d) ? self::actionTypes($d) : '-';
                },
            ],
            [
                'db'        => 'Tab.category', 'dt' => 8,
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
                'db'        => 'Action.enabled', 'dt' => 9,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false, true, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'tabs',
                'alias'      => 'Tab',
                'type'       => 'INNER',
                'conditions' => [
                    'Tab.id = Action.tab_id',
                ],
            ],
        ];
        
        return Datatable::simple($this, $columns, $joins);
        
    }
    
    /**
     * 更新功能
     *
     * @param array $data
     * @param integer $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除功能
     *
     * @param null|integer $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                array_map(
                    function (array $classes, string $action) use ($id) {
                        $this->purge($classes, 'action_id', $action, $id);
                    }, [[class_basename($this), 'ActionGroup'], ['Tab']], ['purge', 'reset']
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 扫描所有控制器中的方法
     *
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    function scan() {
        
        try {
            DB::transaction(function () {
                $this->routes = Route::getRoutes()->getRoutes();
                $this->acronyms = Corp::pluck('acronym')->toArray();
                $this->actionTypes = ActionType::pluck('id', 'name')->toArray();
                $controllers = self::scanDirs(self::siteRoot() . Constant::CONTROLLER_DIR);
                # 获取控制器的名字空间
                $this->controllerNamespaces($controllers);
                # 移除excluded控制器
                $controllerNames = array_diff(
                    $this->controllerNames($controllers),
                    Constant::EXCLUDED_CONTROLLERS
                );
                $selfDefinedMethods = [];
                // remove actions of non-existing controllers
                $ctlrs = $this->groupBy('tab_id')->get(['tab_id'])->toArray();
                $existingCtlrs = Tab::whereIn('id', $ctlrs)->pluck('id')->toArray();
                $ctlrDiffs = array_diff(
                    $existingCtlrs,
                    Tab::whereIn('name', $controllerNames)->pluck('id')->toArray()
                );
                foreach ($ctlrDiffs as $ctlr) {
                    $actions = self::whereTabId($ctlr)->get();
                    foreach ($actions as $a) {
                        $removed = $this->remove($a->id);
                        throw_if(!$removed, new Exception(__('messages.del_fail')));
                    }
                }
                foreach ($controllers as $controller) {
                    $paths = explode('\\', $controller);
                    if (!in_array($paths[sizeof($paths) - 1], Constant::EXCLUDED_CONTROLLERS)) {
                        $obj = new ReflectionClass(ucfirst($controller));
                        $className = $obj->getName();
                        $methods = $obj->getMethods();
                        // remove non-existing methods of current controller
                        $this->delNonExistingMethods($methods, $className);
                        foreach ($methods as $method) {
                            $action = $method->getName();
                            if (
                                $method->class === $className &&
                                !($method->isConstructor()) &&
                                $method->isUserDefined() &&
                                $method->isPublic()
                            ) {
                                $ctlr = $this->controllerName($className);
                                $selfDefinedMethods[$className][$action] = [
                                    'name'            => $this->methodComment($obj, $method),
                                    'method'          => $action,
                                    'remark'          => '',
                                    'tab_id'          => Tab::whereName($ctlr)->first()->id,
                                    'view'            => $this->viewPath($ctlr, $action),
                                    'route'           => $this->actionRoute($ctlr, $action),
                                    'action_type_ids' => $this->actionTypeIds($ctlr, $action),
                                    'js'              => $this->jsPath($ctlr, $action),
                                ];
                            }
                        }
                    }
                    
                }
                foreach ($selfDefinedMethods as $actions) {
                    foreach ($actions as $action) {
                        $a = $this->where([
                            ['tab_id', $action['tab_id']],
                            ['method', $action['method']],
                        ])->first();
                        if ($a) {
                            $a->name = trim($action['name']);
                            $a->route = $action['route'];
                            $a->view = $action['view'];
                            $a->js = $action['js'];
                            $a->action_type_ids = $action['action_type_ids'];
                            $a->save();
                        } else {
                            $this->create([
                                'name'            => trim($action['name']),
                                'method'          => $action['method'],
                                'remark'          => $action['remark'],
                                'tab_id'          => $action['tab_id'],
                                'view'            => $action['view'],
                                'route'           => $action['route'],
                                'action_type_ids' => $action['action_type_ids'],
                                'js'              => $action['js'],
                                'enabled'         => Constant::ENABLED,
                            ]);
                        }
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 返回HTTP请求方法中包含GET以及路由中不带参数的action列表
     *
     * @return array
     */
    function actions() {
        
        $data = self::whereEnabled(1)->get([
            'tab_id', 'name', 'id',
            'action_type_ids', 'route',
        ]);
        $actions = [];
        # 获取HTTP请求类型为GET的Action类型ID
        $id = ActionType::whereName('GET')->first()->id;
        foreach ($data as $action) {
            $tab = Tab::find($action->tab_id);
            if (!$tab) continue;
            if (
                in_array($id, explode(',', $action['action_type_ids'])) &&
                !strpos($action['route'], '{') &&
                $tab->category != 2 # 其他类型控制器
            ) {
                $actions[$action->tab->name][$action->id] = $action['name'] . ' - ' . $action['route'];
            }
        }
        ksort($actions);
        
        return $actions;
        
    }
    
    /**
     * 获取网站根所处的路径
     *
     * @return bool|string
     */
    function siteRoot() {
        
        return substr(__DIR__, 0, stripos(__DIR__, 'app/Models'));
        
    }
    
    /**
     * 获取composer所需的view数据
     *
     * @return array
     */
    function compose() {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $optionAll = [null => '全部'];
            $data = [
                'titles' => [
                    '#', '名称', '方法', '路由', '控制器',
                    'View路径', 'js路径',
                    [
                        'title' => '请求类型',
                        'html' => $this->htmlSelect(
                            ActionType::pluck('name', 'id')->merge($optionAll)->sort(),
                            'filter_action_type'
                        )
                    ],
                    [
                        'title' => '功能类型',
                        'html' => $this->htmlSelect(
                            array_merge($optionAll, ['后台', '前端', '其他']),
                            'filter_category'
                        )
                    ],
                    [
                        'title' => '状态 . 操作',
                        'html' => $this->htmlSelect(
                            array_merge($optionAll, ['已禁用', '已启用']),
                            'filter_enabled'
                        )
                    ]
                ],
                'filter' => true,
            ];
        } else {
            $actionTypeIds = explode(',', Action::find(Request::route('id'))->action_type_ids);
            $selectedActionTypes = ActionType::whereIn('id', $actionTypeIds)->pluck('id');
            $data = [
                'actionTypes'         => ActionType::pluck('name', 'id'),
                'tabs'                => Tab::pluck('name', 'id'),
                'selectedActionTypes' => $selectedActionTypes,
            ];
        }
    
        return $data;
        
    }
    
    /**
     * 返回控制器的完整名字空间路径
     *
     * @param array $controllers
     */
    function controllerNamespaces(array &$controllers) {
        
        $siteRoot = str_replace('/', '\\', $this->siteRoot());
        for ($i = 0; $i < sizeof($controllers); $i++) {
            $controllers[$i] = str_replace('/', '\\', $controllers[$i]);
            $controllers[$i] = str_replace($siteRoot, '', $controllers[$i]);
            $controllers[$i] = str_replace('.php', '', $controllers[$i]);
        }
        
    }
    
    /**
     * 返回去除名字空间路径的控制器名称数组
     *
     * @param array $controllers
     * @return array
     */
    function controllerNames(array $controllers) {
        
        $controllerNames = [];
        foreach ($controllers as $controller) {
            $paths = explode('\\', $controller);
            $controllerNames[] = $paths[sizeof($paths) - 1];
        }
        
        return $controllerNames;
        
    }
    
    /**
     * 根据ActionType IDs返回Http action名称
     *
     * @param string $action_type_ids
     * @return string
     */
    private function actionTypes($action_type_ids) {
        
        $actionTypes = ActionType::whereIn('id', explode(',', $action_type_ids))
            ->where('enabled', 1)->pluck('name')->toArray();
        
        return implode(',', $actionTypes);
        
    }
    
    /**
     * 返回所有控制器的完整路径
     *
     * @param string $rootDir
     * @param array $allData
     * @return array
     */
    private function scanDirs($rootDir, $allData = []) {
        
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
                    $allData = $this->scanDirs($path, $allData);
                }
            }
        }
        
        return $allData;
        
    }
    
    /**
     * 删除指定控制器中不存在的方法
     *
     * @param array $methods
     * @param string $className
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    private function delNonExistingMethods(array $methods, string $className) {
        
        // remove non-existing methods of current controller
        try {
            DB::transaction(function () use ($methods, $className) {
                $currentMethods = $this->methodNames($methods);
                $tabId = Tab::whereName($this->controllerName($className))->first()->id;
                $existingMethods = $this->whereTabId($tabId)->pluck('method')->toArray();
                $methodDiffs = array_diff($existingMethods, $currentMethods);
                foreach ($methodDiffs as $method) {
                    $this->remove(
                        $this->where(['tab_id' => $tabId, 'method' => $method])->first()->id
                    );
                }
                
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 获取指定方法的名称
     *
     * @param $methods
     * @return array
     */
    private function methodNames($methods) {
        
        $methodNames = [];
        /** @var ReflectionMethod $method */
        foreach ($methods as $method) {
            $methodNames[] = $method->getName();
        }
        
        return $methodNames;
        
    }
    
    /**
     * 返回去除名字空间路径的控制器名称
     *
     * @param $controller
     * @return mixed
     */
    private function controllerName($controller) {
        
        $nameSpacePaths = explode('\\', $controller);
        
        return $nameSpacePaths[sizeof($nameSpacePaths) - 1];
        
    }
    
    /**
     * 获取方法备注名称
     *
     * @param ReflectionClass $controllerObj
     * @param ReflectionMethod $method
     * @return mixed|string
     * @throws ReflectionException
     */
    private function methodComment(ReflectionClass $controllerObj, ReflectionMethod $method) {
        
        $comment = $controllerObj->getMethod($method->getName())->getDocComment();
        $name = 'n/a';
        preg_match_all("#\/\*\*\n\s{5}\*[^\*]*\*#", $comment, $matches);
        if (isset($matches[0][0])) {
            $name = str_replace(str_split("\r\n/*"), '', $matches[0][0]);
        } else {
            preg_match_all("#\/\*\*\r\n\s{5}\*[^\*]*\*#", $comment, $matches);
            if (isset($matches[0][0])) {
                $name = str_replace(str_split("\r\n/*"), '', $matches[0][0]);
            }
        }
        
        return $name;
        
    }
    
    /**
     * 获取控制器action对应的View路径
     *
     * @param $controller
     * @param $action
     * @return string
     */
    private function viewPath($controller, $action) {
        
        if (
            in_array($action, self::ACTIONS_WITHOUT_VIEW_AND_JS) ||
            in_array($controller, Constant::EXCLUDED_CONTROLLERS) ||
            !in_array('GET', $this->actionMethods($controller, $action))
        ) {
            return null;
        }
        switch ($action) {
            case 'index':
            case 'create':
            case 'edit':
            case 'show':
            case 'recharge':
                $prefix = Str::singular($this->tableName($controller));
                $prefix = ($prefix === 'corps') ? 'corp' : $prefix;
                $viewPath = $prefix . '.' . $action;
                break;
            case 'menuTabs':
                $viewPath = 'menu.menu_tabs';
                break;
            case 'relationship':
                $viewPath = 'custodian.relationship';
                break;
            default:
                $viewPath = Inflector::singularize(self::tableName($controller)) . '.' . $action;
                break;
        }
        $category = Tab::whereName($controller)->first()->category;
        
        return !$category ? $viewPath
            : ($category == 1 ? 'wechat.' . $viewPath : 'auth.' . $action);
        
    }
    
    /**
     * 返回指定控制器 & 方法对应的Http请求方式
     *
     * @param $controller
     * @param $action
     * @return array|mixed
     */
    private function actionMethods($controller, $action) {
        
        $routes = array_filter(
            $this->routes,
            function (\Illuminate\Routing\Route $route) use ($controller, $action) {
                return stripos(
                        $route->action['controller'] ?? '',
                        '\\' . $controller . '@' . $action
                    ) !== false;
            }
        );
        $methods = [];
        /** @var \Illuminate\Routing\Route $route */
        foreach ($routes as $route) {
            $methods = array_merge($methods, $route->methods);
        }
        
        return array_unique($methods);
        
    }
    
    /**
     * 根据控制器名称返回表名称
     *
     * @param $controller string 控制器类名
     * @return string 数据表名称
     */
    private function tableName($controller) {
        
        $modelName = substr(
            $controller, 0,
            strlen($controller) - strlen('Controller')
        );
        
        return $modelName === 'Squad'
            ? 'classes'
            : Inflector::pluralize(Inflector::tableize($modelName));
        
    }
    
    /**
     * 根据控制器名称和action名称返回action对应的路由名称
     *
     * @param $controller string 控制器名称
     * @param $action string action名称
     * @return mixed 路由名称
     */
    private function actionRoute($controller, $action) {
        
        if (!in_array($controller, Constant::EXCLUDED_CONTROLLERS)) {
            /** @var \Illuminate\Routing\Route $route */
            foreach ($this->routes as $route) {
                $aPos = stripos(
                    $route->action['controller'] ?? '',
                    '\\' . $controller . '@' . $action
                );
                if ($aPos === false) continue;
                $tableName = $this->tableName($controller);
                $uris = explode('/', $route->uri);
                $rPos = stripos($route->uri, $tableName . '/' . $action);
                if ($rPos === 0 || ($rPos === false && array_search($tableName, $uris) === false)) {
                    return $route->uri;
                }
                if (in_array($uris[0], $this->acronyms)) {
                    $uris[0] = '{acronym}';
                }
                
                return implode('/', $uris);
            }
        }
        
        return null;
        
    }
    
    /**
     * 返回指定action的HTTP请求类型名称
     *
     * @param $controller
     * @param $action
     * @return null|string
     */
    private function actionTypeIds($controller, $action) {
        
        if (in_array($controller, Constant::EXCLUDED_CONTROLLERS)) {
            return null;
        }
        $actionTypes = $this->actionMethods($controller, $action);
        $actionTypeIds = ActionType::whereIn('name', $actionTypes)->pluck('id')->toArray();
        
        return implode(',', $actionTypeIds);
        
    }
    
    /**
     * 返回指定action对应的js路径
     *
     * @param $ctlr
     * @param $action
     * @return mixed
     */
    private function jsPath($ctlr, $action) {
        
        if (
            in_array($action, self::ACTIONS_WITHOUT_VIEW_AND_JS) ||
            in_array($ctlr, Constant::EXCLUDED_CONTROLLERS) ||
            !in_array('GET', $this->actionMethods($ctlr, $action))
        ) {
            return null;
        }
        $prefix = Str::singular($this->tableName($ctlr));
        $prefix = ($prefix === 'corps') ? 'corp' : $prefix;
        
        return 'js/' . $prefix . '/' . $action . '.js';
        
    }
    
}
