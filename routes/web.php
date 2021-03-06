<?php

use App\Models\Corp;
use Doctrine\Common\Inflector\Inflector;

/** Helper functions ------------------------------------------------------------------------------------------------ */
if (!function_exists('routes')) {
    /**
     * @param array $routes
     * @param null $prefix
     * @param null $dir
     */
    function routes(array $routes, $prefix = null, $dir = null) {
        
        foreach ($routes as $model => $methods) {
            $table = Inflector::pluralize($model);
            $model = $model == 'class' ? 'squad' : $model;
            $controller = ucfirst(Inflector::camelize($model)) . 'Controller';
            !$dir ?: $controller = ucfirst($dir) . '\\' . $controller;
            foreach ($methods as $method => $reqs) {
                $paths = [$table, $method == 'destroy' ? 'delete' : $method];
                !$prefix ?: $paths = array_merge([$prefix], $paths);
                $verbs = is_numeric($param = array_keys($reqs)[0]) ? $reqs : $reqs[$param];
                is_array($verbs) ?: $verbs = [$verbs];
                is_numeric($param) ?: $paths = array_merge($paths, [$param]);
                Route::match(
                    $verbs, implode('/', $paths),
                    implode('@', [$controller, $method])
                );
            }
        }
        
    }
}
/** 后台路由 ---------------------------------------------------------------------------------------------------------- */
Route::auth();
Route::any('register', 'Auth\LoginController@signup');
Route::get('logout', 'Auth\LoginController@logout');
# 测试及维护用路由
Route::group(['prefix' => ''], function () {
    $c = 'TestController';
    Route::any('test/index', $c . '@index');
    Route::get('listen', $c . '@listen');
});
Route::get('event', 'TestController@event');
Route::get('/', 'HomeController@index');
Route::get('home', 'HomeController@index')->name('home');
Route::get('pages/{id}', 'HomeController@menu');
$default = [
    'index'   => ['get'],
    'create'  => ['get'],
    'store'   => ['post'],
    'edit'    => ['{id?}' => 'get'],
    'update'  => ['{id?}' => 'put'],
    'destroy' => ['{id?}' => 'delete'],
];
$routes = [
    'action'         => [
        'index'  => ['get'],
        'edit'   => ['{id}' => 'get'],
        'update' => ['{id}' => 'put'],
    ],
    'app'            => $default,
    'article'        => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['{id}' => ['get', 'post']],
        'update'  => ['{id}' => 'put'],
        'destroy' => ['{id}' => 'delete'],
    ], 'bed'         => $default,
    'building'       => $default,
    'camera'         => [
        'index' => ['get'],
        'store' => ['post'],
    ],
    'card'           => $default,
    'class'          => $default,
    'column'         => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['{id}' => ['get', 'post']],
        'update'  => ['{id}' => 'put'],
        'destroy' => ['{id}' => 'delete'],
    ],
    'combo_type'     => $default,
    'company'        => $default,
    'consumption'    => [
        'index'  => ['get'],
        'show'   => ['get'],
        'stat'   => ['post'],
        'export' => ['get'],
    ],
    'conference'     => $default,
    'corp'           => array_merge(
        $default, ['recharge' => ['{id}' => ['get', 'put']]]
    ),
    'custodian'      => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['{id}' => ['get', 'post']],
        'update'  => ['{id?}' => 'put'],
        'destroy' => ['{id?}' => 'delete'],
        'issue'   => ['get', 'post'],
        'grant'   => ['get', 'post'],
        'face'    => ['get', 'post'],
    ],
    'department'     => [
        'index'   => ['get', 'post'],
        'create'  => ['{parentId}' => 'get'],
        'store'   => ['post'],
        'edit'    => ['{id}' => 'get'],
        'update'  => ['{id}' => 'put'],
        'destroy' => ['{id}' => ['delete', 'get']],
    ],
    'educator'       => [
        'index'    => ['get'],
        'create'   => ['get', 'post'],
        'store'    => ['post'],
        'edit'     => ['{id}' => ['get', 'post']],
        'update'   => ['{id?}' => 'put'],
        'recharge' => ['{id}' => ['get', 'put']],
        'destroy'  => ['{id?}' => 'delete'],
        'import'   => ['post'],
        'export'   => ['get', 'post'],
        'issue'    => ['get', 'post'],
        'grant'    => ['get', 'post'],
        'face'     => ['get', 'post'],
    ],
    'evaluate'       => $default,
    'event'          => $default,
    'exam'           => $default,
    'exam_type'      => $default,
    'face'           => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'destroy' => ['{id?}' => 'delete'],
    ],
    'flow'           => $default,
    'flow_type'      => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['{id?}' => ['get', 'post']],
        'update'  => ['{id?}' => 'put'],
        'destroy' => ['{id?}' => 'delete'],
    ],
    'grade'          => $default,
    'group'          => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'edit'    => ['{id}' => ['get', 'post']],
        'destroy' => ['{id?}' => 'delete'],
        'update'  => ['{id}' => 'put'],
        'store'   => ['post'],
    ],
    'icon'           => $default,
    'indicator'      => $default,
    'init'           => [
        'index' => ['get', 'post'],
    ],
    'major'          => $default,
    'menu'           => [
        'index'   => ['get', 'post'],
        'create'  => ['{parentId}' => 'get'],
        'store'   => ['post'],
        'edit'    => ['{id}' => 'get'],
        'update'  => ['{id}' => 'put'],
        'destroy' => ['{id}' => ['delete', 'get']],
        'sort'    => ['{id}' => ['get', 'post']],
    ],
    'message'        => [
        'index'   => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['{id}' => 'get'],
        'update'  => ['{id}' => 'put'],
        'show'    => ['{id}' => 'get'],
        'send'    => ['post'],
        'destroy' => ['{id?}' => 'delete'],
    ],
    'module'         => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['{id}' => ['get', 'post']],
        'update'  => ['{id?}' => 'get'],
        'destroy' => ['{id?}' => 'delete'],
    ],
    'operator'       => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['{id}' => ['get', 'post']],
        'update'  => ['{id?}' => 'get'],
        'destroy' => ['{id?}' => 'delete'],
    ],
    'participant'    => [
        'index' => ['get'],
        'store' => ['post'],
        'show'  => ['{id}' => 'get'],
    ],
    'partner'        => array_merge(
        $default, ['recharge' => ['{id}' => ['get', 'put']]]
    ),
    'passage_log'    => [
        'index'  => ['get'],
        'store'  => ['post'],
        'export' => ['post'],
    ],
    'passage_rule'   => array_merge(
        $default,
        ['issue' => ['post']]
    ),
    'poll'           => $default,
    'poll_topic'     => $default,
    'prize'          => $default,
    'room'           => $default,
    'room_type'      => $default,
    'school'         => array_merge(
        $default, ['recharge' => ['{id}' => ['get', 'put']]]
    ),
    'score'          => [
        'index'   => ['get'],
        'create'  => ['{examId?}' => 'get'],
        'edit'    => ['{id}/{examId?}' => 'get'],
        'store'   => ['post'],
        'update'  => ['{id?}' => 'put'],
        'destroy' => ['{id?}' => 'delete'],
        'rank'    => ['{examId}' => 'get'],
        'stat'    => ['get', 'post'],
        'import'  => ['{examId?}' => ['get', 'post']],
        'export'  => ['{examId?}' => ['get', 'post']],
        'send'    => ['post'],
    ],
    'score_range'    => array_merge(
        $default,
        ['stat' => ['get', 'post']]
    ),
    'score_total'    => [
        'index' => ['get'],
        'stat'  => ['{examId}' => 'get'],
    ],
    'semester'       => $default,
    'student'        => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['{id}' => ['get', 'post']],
        'update'  => ['{id?}' => 'put'],
        'destroy' => ['{id?}' => 'delete'],
        'import'  => ['post'],
        'export'  => ['get', 'post'],
        'issue'   => ['get', 'post'],
        'grant'   => ['get', 'post'],
        'face'    => ['get', 'post'],
    ],
    'subject'        => $default,
    'subject_module' => $default,
    'tab'            => [
        'index'  => ['get'],
        'edit'   => ['{id}' => 'get'],
        'update' => ['{id}' => 'put'],
    ],
    'tag'            => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['{id?}' => ['get', 'post']],
        'update'  => ['{id?}' => 'get'],
        'destroy' => ['{id?}' => 'delete'],
    ],
    'template'       => [
        'index'   => ['get'],
        'config'  => ['get'],
        'store'   => ['post'],
        'fetch'   => ['post'],
        'destroy' => ['{id?}' => 'delete'],
    ],
    'turnstile'      => [
        'index' => ['get'],
        'store' => ['post'],
    ],
    'user'           => [
        'edit'   => ['get'],
        'update' => ['put'],
        'reset'  => ['get', 'post'],
        'info'   => ['get'],
    ],
    'wap'            => [
        'index'  => ['get'],
        'edit'   => ['{id}' => ['get', 'post']],
        'update' => ['{id}' => 'put'],
    ],
    # 演示(微信端)
    'demo'           => [
        'index'     => ['get'],
        'safe'      => ['get'],
        'wisdom'    => ['get'],
        'classroom' => ['get'],
        'info'      => ['get'],
    ],
];
routes($routes);
/** 微信端路由 -------------------------------------------------------------------------------------------------------- */
Route::get('msg/{code}', 'Wechat\InfoController@detail');
$routes = [
    'info'      => [
        'index'   => ['get', 'post'],
        'create'  => ['get', 'post'],
        'edit'    => ['{id?}' => ['get', 'post']],
        'update'  => ['{id?}' => 'put'],
        'show'    => ['{id}' => ['get', 'post', 'delete']],
        'destroy' => ['{id}' => 'delete'],
        'send'    => ['post'],
    ],
    'mobile'    => [
        'index'   => ['get'],
        'module'  => ['get'],
        'article' => ['get'],
    ],
    'mark'      => [
        'index'   => ['get', 'post'],
        'detail'  => ['get', 'post'],
        'graph'   => ['get', 'post'],
        'analyze' => ['get'],
        'stat'    => ['get'],
    ],
    'home_work' => [
        'index' => ['get'],
    ],
];
foreach (Corp::pluck('acronym') as $acronym) {
    /** 应用入口 */
    Route::group(['prefix' => $acronym . '/wechat'], function () {
        $c = 'Wechat\WechatController';
        Route::get('/{id?}', $c . '@index');
        Route::get('/schools/{id?}', $c . '@schools');
        Route::get('/roles/{id?}', $c . '@roles');
        Route::get('/signup/{id}', $c . '@signup');
    });
    Route::match(
        ['get', 'post'], $acronym . '/sync',
        'Wechat\SyncController@sync'
    );
    routes($routes, $acronym, 'Wechat');
}
