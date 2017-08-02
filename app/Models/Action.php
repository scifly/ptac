<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\ActionType as ActionType;
use Illuminate\Support\Facades\Route;
use ReflectionClass;

/**
 * App\Models\Action
 *
 * @property int $id
 * @property string $name 功能名称
 * @property string $method 方法名称
 * @property string $remark 备注
 * @property string $controller 所属controller类名
 * @property string|null $view 对应的blade view名
 * @property string|null $route 对应的route名称
 * @property string|null $js 定制JS脚本路径
 * @property int|null $datatable 是否使用datatable
 * @property int|null $parsley 是否使用parsley
 * @property int|null $select2 是否使用select2
 * @property int|null $chart 是否使用chart
 * @property int|null $map 是否使用map
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @property string|null $action_type_ids HTTP请求类型IDs
 * @method static Builder|Action whereActionTypeIds($value)
 * @method static Builder|Action whereChart($value)
 * @method static Builder|Action whereController($value)
 * @method static Builder|Action whereCreatedAt($value)
 * @method static Builder|Action whereDatatable($value)
 * @method static Builder|Action whereEnabled($value)
 * @method static Builder|Action whereId($value)
 * @method static Builder|Action whereJs($value)
 * @method static Builder|Action whereMap($value)
 * @method static Builder|Action whereMethod($value)
 * @method static Builder|Action whereName($value)
 * @method static Builder|Action whereParsley($value)
 * @method static Builder|Action whereRemark($value)
 * @method static Builder|Action whereRoute($value)
 * @method static Builder|Action whereSelect2($value)
 * @method static Builder|Action whereUpdatedAt($value)
 * @method static Builder|Action whereView($value)
 * @mixin \Eloquent
 */
class Action extends Model {
    
    protected $fillable = [
        'name',
        'method',
        'remark',
        'controller',
        'view',
        'route',
        'js',
        'datatable',
        'parsley',
        'select2',
        'chart',
        'map',
        'action_type_ids',
        'enabled'
    ];
    // protected $actionType;
    protected $actionTypes;
    protected $excludedControllers = [
        'Controller',
        'ForgotPasswordController',
        'LoginController',
        'RegisterController',
        'ResetPasswordController',
        'HomeController',
        'TestController',
        // 'ActionController'
    ];
    protected $routes;
    # 控制器路径
    protected $dir = '/media/sf_sandbox/urlshortener/app/Http/Controllers';
    
    /**
     * 根据ActionType IDs返回Http action名称
     *
     * @param $action_type_ids
     * @return string
     */
    public function actionTypes($action_type_ids) {
        
        $actionTypes = [];
        $actionTypeIds = explode(',', $action_type_ids);
        foreach ($actionTypeIds as $actionTypeId) {
            $actionType = ActionType::whereId($actionTypeId)->where('enabled', 1)->first();
            $actionTypes[] = $actionType->name;
        }
        return implode(',', $actionTypes);
        
    }
    
    /**
     * 返回当前action包含的卡片
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tabs() {
        
        return $this->belongsToMany('App\Models\Tab', 'tabs_actions')
            ->withPivot('default', 'enabled')
            ->withTimestamps();
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Action.id', 'dt' => 0],
            ['db' => 'Action.name', 'dt' => 1],
            ['db' => 'Action.method', 'dt' => 2],
            ['db' => 'Action.controller', 'dt' => 3],
            ['db' => 'Action.view', 'dt' => 4],
            ['db' => 'Action.js', 'dt' => 5],
            ['db' => 'Action.created_at', 'dt' => 6],
            ['db' => 'Action.updated_at', 'dt' => 7],
            [
                'db' => 'Aciton.action_type_ids', 'dt' => 8,
                'formatter' => function($d) {
                    return $this->actionTypes($d);
                }
            ],
            [
                'db' => 'Action.enabled', 'dt' => 9,
                'formatter' => function($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        
        return Datatable::simple($this, $columns);
        
    }
    
    public function scan() {
    
        $actionType = new ActionType();
        $this->actionTypes = $actionType->pluck('id', 'name')->toArray();
        $this->routes = Route::getRoutes()->getRoutes();
        $controllers = $this->scanDirectories($this->dir);
        $this->returnNamespaces($controllers);
        $selfDefinedMethods = [];
        $ss = $this->groupBy('controller')->get(['controller']);
        
        dd($ss);
        
        foreach ($controllers as $controller) {
            // dd($controller);
            $obj = new ReflectionClass(ucfirst($controller));
            $className = $obj->getName();
            $methods = $obj->getMethods();
            // dd($methods);
            foreach ($methods as $method) {
                $action = $method->getName();
                if (
                    $method->class === $className &&
                    !($method->isConstructor()) &&
                    $method->isUserDefined() &&
                    $method->isPublic()
                ) {
                    $selfDefinedMethods[$className][$action]['name'] = '';
                    $selfDefinedMethods[$className][$action]['method'] = $action;
                    $selfDefinedMethods[$className][$action]['remark'] = '';
                    $nameSpacePaths = explode('\\', $className);
                    $ctlr = $nameSpacePaths[sizeof($nameSpacePaths) - 1];
                    $selfDefinedMethods[$className][$action]['controller'] = $ctlr;
                    $selfDefinedMethods[$className][$action]['view'] = $this->getViewPath($ctlr, $action);
                    $selfDefinedMethods[$className][$action]['route'] = $this->getRoute($ctlr, $action);
                    $selfDefinedMethods[$className][$action]['action_type_ids'] = $this->getActionTypeIds($ctlr, $action);
                    if (!in_array($ctlr, $this->excludedControllers)) {
                        switch ($action) {
                            case 'index':
                            case 'create':
                            case 'edit':
                                $selfDefinedMethods[$className][$action]['js'] =
                                    'js/' . str_singular($this->getTableName($ctlr)) . '/' . $action . '.js';
                                break;
                            default:
                                $selfDefinedMethods[$className][$action]['js'] = NULL;
                                break;
                        }
                    } else {
                        $selfDefinedMethods[$className][$action]['js'] = NULL;
                    }
                }
            }
        }
        foreach ($selfDefinedMethods as $actions) {
            foreach ($actions as $action) {
                $data = [
                    'name' => $action['name'],
                    'method' => $action['method'],
                    'remark' => $action['remark'],
                    'controller' => $action['controller'],
                    'view' => $action['view'],
                    'route' => $action['route'],
                    'action_type_ids' => $action['action_type_ids'],
                    'js' => $action['js'],
                    'datatable' => NULL,
                    'parsley' => NULL,
                    'select2' => NULL,
                    'chart' => NULL,
                    'map' => NULL,
                    'enabled' => 1
                ];
                
                $this->create($data);
            }
        }
        
    }
    
    private function scanDirectories($rootDir, $allData = array()) {
        
        // set filenames invisible if you want
        $invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
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
                    $allData = $this->scanDirectories($path, $allData);
                }
            }
        }
        
        return $allData;
        
    }
    
    private function returnNamespaces(&$controllers) {
        
        for ($i = 0; $i < sizeof($controllers); $i++) {
            $controllers[$i] = str_replace('/', '\\', $controllers[$i]);
            $controllers[$i] = str_replace('\\media\\sf_sandbox\\urlshortener\\', '', $controllers[$i]);
            $controllers[$i] = str_replace('.php', '', $controllers[$i]);
        }
        
    }
    
    private function getViewPath($controller, $action) {
        
        if (!in_array($controller, $this->excludedControllers)) {
            switch ($action) {
                case 'index':
                case 'create':
                case 'edit':
                case 'show':
                    $viewPath = str_singular($this->getTableName($controller)) . '.' . $action;
                    break;
                default:
                    $viewPath = '';
            }
            return $viewPath;
        }
        return '';
    }
    
    private function getActionTypeIds($controller, $action) {
        
        $action = ($action == 'destroy' ? 'delete' : $action);
        if (!in_array($controller, $this->excludedControllers)) {
            $route = $this->getTableName($controller) . '/' . $action;
            $actionTypeIds = [];
            foreach ($this->routes as $r) {
                if (strpos($r->uri, $route) === 0) {
                    foreach ($r->methods as $method) {
                        $actionTypeIds[] = $this->actionTypes[$method];
                    }
                }
            }
            return implode(',', $actionTypeIds);
        }
        return NULL;
        
    }
    
    /**
     * 根据控制器名称和action名称返回对应的路由名称
     *
     * @param $controller string 控制器名称
     * @param $action string action名称
     * @return mixed 路由名称
     */
    private function getRoute($controller, $action) {
        
        $action = ($action == 'destroy' ? 'delete' : $action);
        if (!in_array($controller, $this->excludedControllers)) {
            $route = $this->getTableName($controller) . '/' . $action;
            foreach ($this->routes as $r) {
                if (strpos($r->uri, $route) === 0) {
                    return $r->uri;
                }
            }
        }
        return NULL;
        
    }
    
    /**
     * 根据控制器名称返回表名称
     *
     * @param $controller string 控制器类名
     * @return string 数据表名称
     */
    private function getTableName($controller) {
        
        $modelName = substr($controller, 0, strlen($controller) - strlen('Controller'));
        $modelName = 'App\\Models\\' . $modelName;
        if ($modelName === 'App\\Models\\Action') {
            $tableName = $this->getTable();
        } else {
            $model = new $modelName;
            /** @noinspection PhpUndefinedMethodInspection */
            $tableName = $model->getTable();
            unset($model);
        }
        
        return $tableName;
        
    }
    
}
