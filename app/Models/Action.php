<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\ActionRequest;
use App\Models\ActionType as ActionType;
use Carbon\Carbon;
use Doctrine\Common\Inflector\Inflector;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    protected $fillable = [
        'name', 'method', 'remark',
        'controller', 'view', 'route',
        'js', 'action_type_ids', 'enabled',
    ];
    const EXCLUDED_CONTROLLERS = [
        'Controller',
        'ForgotPasswordController',
        'LoginController',
        'RegisterController',
        'ResetPasswordController',
        'HomeController',
        'TestController',
        'HomeWorkController',
        'MessageCenterController',
        'AttendanceController',
        'MobileSiteController',
        'ScoreCenterController'
    ];
    protected $routes;
    # 控制器相对路径
    const CONTROLLER_DIR = 'app/Http/Controllers';

    /**
     * 返回当前action包含的卡片
     *
     * @return HasMany
     */
    public function tabs() { return $this->hasMany('App\Models\Tab'); }

    /**
     * 返回HTTP请求方法中包含GET以及路由中不带参数的action列表
     *
     * @return array
     */
    static function actions() {

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
     * 功能列表
     *
     * @return array
     */
    static function datatable() {
        
        $columns = [
            ['db' => 'Action.id', 'dt' => 0],
            [
                'db' => 'Action.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return empty($d) ? '-' : $d;
                },
            ],
            [
                'db' => 'Action.method', 'dt' => 2,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Datatable::BADGE_GREEN, $d) : '-';
                },
            ],
            [
                'db' => 'Action.route', 'dt' => 3,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Datatable::BADGE_YELLOW, $d) : '-';
                },
            ],
            [
                'db' => 'Action.controller', 'dt' => 4,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Datatable::BADGE_RED, $d) : '-';
                },
            ],
            [
                'db' => 'Action.view', 'dt' => 5,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Datatable::BADGE_LIGHT_BLUE, $d) : '-';
                },
            ],
            [
                'db' => 'Action.js', 'dt' => 6,
                'formatter' => function ($d) {
                    return !empty($d) ? sprintf(Datatable::BADGE_MAROON, $d) : '-';
                },
            ],
            [
                'db' => 'Action.action_type_ids', 'dt' => 7,
                'formatter' => function ($d) {
                    return !empty($d) ? self::actionTypes($d) : '-';
                },
            ],
            [
                'db' => 'Action.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    $id = $row['id'];
                    $status = $d ? Datatable::DT_ON : Datatable::DT_OFF;
                    $editLink = sprintf(Datatable::DT_LINK_EDIT, 'edit_' . $id);
                    return $status . str_repeat('&nbsp;', 3) . $editLink;
                },
            ],
        ];
        
        return Datatable::simple(self::getModel(), $columns);
        
    }
    
    /**
     * 保存新创建的Action记录
     *
     * @param ActionRequest $request
     * @return bool
     */
    static function store(ActionRequest $request) {

        # 创建新的Action记录及卡片绑定记录
        return self::create($request->all()) ? true : false;

    }

    /**
     * 修改指定的Action记录
     *
     * @param ActionRequest $request
     * @param $actionId
     * @return bool
     */
    static function modify(ActionRequest $request, $actionId) {

        $action = self::find($actionId);
        if (!isset($action)) { return false; }
        
        return $action->update($request->all());

    }
    
    /**
     * 移除指定的Action记录
     *
     * @param $actionId
     * @return bool|mixed
     * @throws Exception|Throwable
     */
    static function remove($actionId) {
        
        $action = self::find($actionId);
        if (!isset($action)) { return false; }
        
        return $action->delete();
        
    }
    
    /**
     * 扫描所有控制器中的方法
     *
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    public function scan() {

        $this->routes = Route::getRoutes()->getRoutes();
        $controllers = self::scanDirectories(self::getSiteRoot() . self::CONTROLLER_DIR);
        # 获取控制器的名字空间
        $this->getControllerNamespaces($controllers);

        # 移除excluded控制器
        $controllerNames = array_diff(
            $this->getControllerNames($controllers),
            self::EXCLUDED_CONTROLLERS
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
            if (!in_array($paths[sizeof($paths) - 1], self::EXCLUDED_CONTROLLERS)) {
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
                        $ctlr = $this->getControllerName($className);
                        $selfDefinedMethods[$className][$action] = [
                            'name' => $this->getMethodComment($obj, $method),
                            'method' => $action,
                            'remark' => '',
                            'controller' => $ctlr,
                            'view' => $this->getViewPath($ctlr, $action),
                            'route' => $this->getRoute($ctlr, $action),
                            'action_type_ids' => $this->getActionTypeIds($ctlr, $action),
                            'js' => $this->getJsPath($ctlr, $action),
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
                    $a->name = $action['name'];
                    $a->route = $action['route'];
                    $a->view = $action['view'];
                    $a->js = $action['js'];
                    $a->action_type_ids = $action['action_type_ids'];
                    $a->save();
                } else {
                    $this->create([
                        'name' => $action['name'],
                        'method' => $action['method'],
                        'remark' => $action['remark'],
                        'controller' => $action['controller'],
                        'view' => $action['view'],
                        'route' => $action['route'],
                        'action_type_ids' => $action['action_type_ids'],
                        'js' => $action['js'],
                        'enabled' => 1,
                    ]);
                }
            }
        }

        return true;
    }
    
    /**
     * 获取网站根所处的路径
     *
     * @return bool|string
     */
    static function getSiteRoot() {
        
        return substr(__DIR__, 0, stripos(__DIR__, 'app/Models'));
        
    }
    
    /**
     * 返回控制器的完整名字空间路径
     *
     * @param $controllers
     */
    static function getControllerNamespaces(&$controllers) {
        
        $siteRoot = str_replace('/', '\\', self::getSiteRoot());
        for ($i = 0; $i < sizeof($controllers); $i++) {
            $controllers[$i] = str_replace('/', '\\', $controllers[$i]);
            $controllers[$i] = str_replace($siteRoot, '', $controllers[$i]);
            $controllers[$i] = str_replace('.php', '', $controllers[$i]);
        }
        
    }
    
    /**
     * 返回去除名字空间路径的控制器名称数组
     *
     * @param $controllers
     * @return array
     */
    static function getControllerNames($controllers) {
        
        $controllerNames = [];
        foreach ($controllers as $controller) {
            $paths = explode('\\', $controller);
            $controllerNames[] = $paths[sizeof($paths) - 1];
        }
        
        return $controllerNames;
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 返回所有控制器的完整路径
     *
     * @param $rootDir
     * @param array $allData
     * @return array
     */
    private static function scanDirectories($rootDir, $allData = []) {

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
                    $allData = self::scanDirectories($path, $allData);
                }
            }
        }
        return $allData;

    }
    
    /**
     * 删除指定控制器中不存在的方法
     *
     * @param $methods
     * @param $className
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    private static function delNonExistingMethods($methods, $className) {

        // remove non-existing methods of current controller
        $currentMethods = self::getMethodNames($methods);
        $existingMethods = [];
        $controllerName = self::getControllerName($className);
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
    private static function getMethodNames($methods) {

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
    private static function getControllerName($controller) {

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
    private static function getMethodComment(ReflectionClass $controllerObj, ReflectionMethod $method) {

        print_r($controllerObj);
        $comment = $controllerObj->getMethod($method->getName())->getDocComment();
        print_r($comment);
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
    private function getViewPath($controller, $action) {

        if (!in_array($controller, self::EXCLUDED_CONTROLLERS)) {
            switch ($action) {
                case 'index':
                case 'create':
                case 'edit':
                case 'show':
                    $prefix = str_singular($this->getTableName($controller));
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
                    $viewPath = Inflector::singularize(self::getTableName($controller)) . '.' . $action;
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
    private static function getTableName($controller) {

        $modelName = substr(
            $controller, 0,
            strlen($controller) - strlen('Controller')
        );
        if ($modelName === 'Squad') {
            return 'classes';
        }

        return Inflector::pluralize(Inflector::tableize($modelName));

    }

    /**
     * 根据控制器名称和action名称返回action对应的路由名称
     *
     * @param $controller string 控制器名称
     * @param $action string action名称
     * @return mixed 路由名称
     */
    private function getRoute($controller, $action) {

        $action = ($action == 'destroy' ? 'delete' : $action);
        if (!in_array($controller, self::EXCLUDED_CONTROLLERS)) {
            $route = $this->getTableName($controller) . '/' . $action;
            foreach ($this->routes as $r) {
                if (stripos($r->uri, $route) === 0) {
                    return $r->uri;
                }
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
    private function getActionTypeIds($controller, $action) {

        $action = ($action == 'destroy' ? 'delete' : $action);
        $actionTypes = ActionType::pluck('id', 'name')->toArray();
        if (!in_array($controller, self::EXCLUDED_CONTROLLERS)) {
            $route = $this->getTableName($controller) . '/' . $action;
            $actionTypeIds = [];
            foreach ($this->routes as $r) {
                if (stripos($r->uri, $route) === 0) {
                    foreach ($r->methods as $method) {
                        $actionTypeIds[] = $actionTypes[$method];
                    }
                }
            }

            return implode(',', $actionTypeIds);
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
    private function getJsPath($ctlr, $action) {

        if (!in_array($ctlr, self::EXCLUDED_CONTROLLERS)) {
            $prefix = str_singular($this->getTableName($ctlr));
            $prefix = ($prefix === 'corps') ? 'corp' : $prefix;
            if (in_array($action, ['destroy', 'store', 'update', 'sort', 'move', 'rankTabs'])) {
                return null;
            }
            // if (in_array($action, ['destroy', 'store', 'update', 'sort', 'move', 'rankTabs', 'show'])) {
            //     return null;
            // }
            return 'js/' . $prefix . '/' . $action . '.js';
        }

        return null;

    }
    
    /**
     * 根据ActionType IDs返回Http action名称
     *
     * @param $action_type_ids
     * @return string
     */
    private static function actionTypes($action_type_ids) {
        
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

}
