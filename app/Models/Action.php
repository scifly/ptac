<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Models\ActionType as ActionType;
use Carbon\Carbon;
use Doctrine\Common\Inflector\Inflector;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
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
 * @property string|null $view 对应的blade view名
 * @property string $method
 * @property string|null $js
 * @property string|null $route
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Tab[] $tabs
 * @method static Builder|Action whereActionTypeIds($value)
 * @method static Builder|Action whereController($value)
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
 * @mixin Eloquent
 */
class Action extends Model {
    
    use ModelTrait;
    
    const ACTIONS_WITHOUT_VIEW_AND_JS = [
        'destroy', 'store', 'update',
        'sort', 'move', 'rankTabs', 'sanction',
    ];
    protected $fillable = [
        'name', 'method', 'remark',
        'controller', 'view', 'route',
        'js', 'action_type_ids', 'enabled',
    ];
    protected $routes;
    
    public $type = 1;
    /**
     * 返回当前action包含的卡片
     *
     * @return HasMany
     */
    function tabs() { return $this->hasMany('App\Models\Tab'); }
    
    /**
     * 返回HTTP请求方法中包含GET以及路由中不带参数的action列表
     *
     * @return array
     */
    function actions() {
        
        $data = self::whereEnabled(1)->get([
            'controller', 'name', 'id',
            'action_type_ids', 'route',
        ]);
        $actions = [];
        # 获取HTTP请求类型为GET的Action类型ID
        $id = ActionType::whereName('GET')->first()->id;
        foreach ($data as $action) {
            if (
                in_array($id, explode(',', $action['action_type_ids'])) &&
                !strpos($action['route'], '{')
            ) {
                $actions[$action->controller][$action->id] = $action['name'] . ' - ' . $action['route'];
            }
        }
        ksort($actions);
        
        return $actions;
        
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
     * 删除指定功能的所有相关数据
     *
     * @param integer $id
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                ActionGroup::whereActionId($id)->delete();
                $this->delRelated('action_id', 'Tab', $id);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 从所有功能中移除指定的功能请求类型
     *
     * @param integer $actionTypeId
     * @throws Throwable
     */
    function removeActionType($actionTypeId) {
        
        try {
            DB::transaction(function () use ($actionTypeId) {
                $actions = $this->where('action_type_id', $actionTypeId)->get();
                foreach ($actions as $action) {
                    $actionTypeIds = array_diff(explode(',', $action->action_type_ids), [$actionTypeId]);
                    $action->update(['action_type_ids' => implode(',', $actionTypeIds)]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
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
                    return !empty($d) ? sprintf(Snippet::BADGE_GREEN, $d) : '-';
                },
            ],
            [
                'db'        => 'Action.route', 'dt' => 3,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Snippet::BADGE_YELLOW, $d) : '-';
                },
            ],
            [
                'db'        => 'Action.controller', 'dt' => 4,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Snippet::BADGE_RED, $d) : '-';
                },
            ],
            [
                'db'        => 'Action.view', 'dt' => 5,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Snippet::BADGE_LIGHT_BLUE, $d) : '-';
                },
            ],
            [
                'db'        => 'Action.js', 'dt' => 6,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Snippet::BADGE_MAROON, $d) : '-';
                },
            ],
            [
                'db'        => 'Action.action_type_ids', 'dt' => 7,
                'formatter' => function ($d) {
                    return !empty($d) ? self::actionTypes($d) : '-';
                },
            ],
            [
                'db'        => 'Action.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    $id = $row['id'];
                    $editLink = sprintf(Snippet::DT_LINK_EDIT, 'edit_' . $id);
                    
                    return Snippet::status($d) . $editLink;
                },
            ],
        ];
        
        return Datatable::simple($this->getModel(), $columns);
        
    }
    
    /**
     * 根据ActionType IDs返回Http action名称
     *
     * @param string $action_type_ids
     * @return string
     */
    private function actionTypes($action_type_ids) {
        
        $actionTypes = [];
        $actionTypeIds = explode(',', $action_type_ids);
        foreach ($actionTypeIds as $actionTypeId) {
            $actionType = ActionType::whereId($actionTypeId)->where('enabled', 1)->first();
            if ($actionType) {
                $actionTypes[] = $actionType->name;
            }
        }
        
        return implode(', ', $actionTypes);
        
    }
    
    /**
     * 扫描所有控制器中的方法
     *
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    function scan() {
        
        $this->routes = Route::getRoutes()->getRoutes();
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
        $ctlrs = $this->groupBy('controller')->get(['controller'])->toArray();
        $existingCtlrs = [];
        foreach ($ctlrs as $ctlr) {
            $existingCtlrs[] = $ctlr['controller'];
        }
        $ctlrDiffs = array_diff($existingCtlrs, $controllerNames);
        foreach ($ctlrDiffs as $ctlr) {
            $actions = $this->where('controller', $ctlr)->get();
            foreach ($actions as $a) {
                if (!$this->remove($a->id)) {
                    return false;
                };
            }
            # $this->where('controller', $ctlr)->delete();
        }
        foreach ($controllers as $controller) {
            $paths = explode('\\', $controller);
            if (!in_array($paths[sizeof($paths) - 1], Constant::EXCLUDED_CONTROLLERS)) {
                $obj = new ReflectionClass(ucfirst($controller));
                $className = $obj->getName();
                $methods = $obj->getMethods();
                // remove non-existing methods of current controller
                try {
                    $this->delNonExistingMethods($methods, $className);
                } catch (Exception $e) {
                    throw $e;
                }
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
                            'controller'      => $ctlr,
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
                    ['controller', $action['controller']],
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
                        'controller'      => $action['controller'],
                        'view'            => $action['view'],
                        'route'           => $action['route'],
                        'action_type_ids' => $action['action_type_ids'],
                        'js'              => $action['js'],
                        'enabled'         => Constant::ENABLED,
                    ]);
                }
            }
        }
        
        return true;
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
     * 获取网站根所处的路径
     *
     * @return bool|string
     */
    function siteRoot() {
        
        return substr(__DIR__, 0, stripos(__DIR__, 'app/Models'));
        
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
    
    /** Helper functions -------------------------------------------------------------------------------------------- */

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
     * 删除功能
     *
     * @param null|integer $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
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
        $currentMethods = self::methodNames($methods);
        $existingMethods = [];
        $controllerName = self::controllerName($className);
        $results = self::whereController($controllerName)->get(['method'])->toArray();
        foreach ($results as $result) {
            $existingMethods[] = $result['method'];
        }
        $methodDiffs = array_diff($existingMethods, $currentMethods);
        foreach ($methodDiffs as $method) {
            $a = self::where([
                ['controller', $controllerName],
                ['method', $method],
            ])->first();
            try {
                self::remove($a->id);
            } catch (Exception $e) {
                throw $e;
            }
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
        
        if (in_array($action, self::ACTIONS_WITHOUT_VIEW_AND_JS)) {
            return null;
        }
        if (!in_array($controller, Constant::EXCLUDED_CONTROLLERS)) {
            switch ($action) {
                case 'index':
                case 'create':
                case 'edit':
                case 'show':
                    $prefix = str_singular($this->tableName($controller));
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
            
            return $viewPath;
        }
        
        return '';
        
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
        
        $action = ($action == 'destroy' ? 'delete' : $action);
        if (!in_array($controller, Constant::EXCLUDED_CONTROLLERS)) {
            /** @var \Illuminate\Routing\Route $route */
            foreach ($this->routes as $route) {
                if ($action == 'ActionController') {
                    dd($route);
                }
                $aPos = stripos(
                    $route->action['controller'],
                    $controller . '@' . $action
                );
                if ($aPos === false) { continue; }
                $rPos = stripos(
                    $this->tableName($controller) . '/' . $action,
                    $route->uri
                );
                if ($rPos === false || $rPos === 0) {
                    return $route->uri;
                }
                $uris = explode('/', $route->uri);
                $uris[0] = '{acronym}';
                return implode('/', $uris);
                /*else if ( $rPos === 0) {
                    return $route->uri;
                } else if (substr($route->uri, $rPos - 1, 1) == '_') {
                    continue;
                } else {
                    $uris = explode('/', $route->uri);
                    $uris[0] = '{acronym}';
                    return implode('/', $uris);
                }*/
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
        
        $action = ($action == 'destroy' ? 'delete' : $action);
        $actionTypes = ActionType::pluck('id', 'name')->toArray();
        if (!in_array($controller, Constant::EXCLUDED_CONTROLLERS)) {
            $route = $this->tableName($controller) . '/' . $action;
            $actionTypeIds = [];
            foreach ($this->routes as $r) {
                $pos = stripos($r->uri, $route);
                if ($pos === false) {
                    continue;
                }
                foreach ($r->methods as $method) {
                    $actionTypeIds[] = $actionTypes[$method];
                }
                return implode(',', $actionTypeIds);
            }
    
        }
        
        return null;
        
    }
    
    /**
     * 返回指定action对应的js路径
     *
     * @param $ctlr
     * @param $action
     * @return mixed
     */
    private function jsPath($ctlr, $action) {
        
        if (!in_array($ctlr, Constant::EXCLUDED_CONTROLLERS)) {
            $prefix = str_singular($this->tableName($ctlr));
            $prefix = ($prefix === 'corps') ? 'corp' : $prefix;
            if (in_array($action, self::ACTIONS_WITHOUT_VIEW_AND_JS)) {
                return null;
            }
            
            return 'js/' . $prefix . '/' . $action . '.js';
        }
        
        return null;
        
    }
    
}
