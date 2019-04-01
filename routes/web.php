<?php
include_once 'common.php';

use App\Helpers\Broadcaster;
use App\Helpers\HttpStatusCode;
use App\Models\Corp;
use Doctrine\Common\Inflector\Inflector;

Route::auth();
Route::any('register', function () { return redirect('login'); });
Route::get('logout', 'Auth\LoginController@logout');
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
Route::any('test/index', 'TestController@index');
Route::get('listen', 'TestController@listen');
Route::get('event', function () {
    (new Broadcaster)->broadcast([
        'userId'     => Auth::id() ?? 1,
        'title'      => '广播测试',
        'statusCode' => HttpStatusCode::OK,
        'message'    => '工作正常',
    ]);
});
/** 菜单入口路由 */
Route::get('pages/{id}', 'HomeController@menu');
$default = [
    'index'   => ['get'],
    'create'  => ['get'],
    'store'   => ['post'],
    'edit'    => ['get' => '{id?}'],
    'update'  => ['get' => '{id?}'],
    'destroy' => ['delete' => '{id?}'],
];
$routes = [
    'action'                 => [
        'index'  => ['get'],
        'edit'   => ['get' => '{id}'],
        'update' => ['put' => '{id}'],
    ],
    'app'                    => [
        'index'   => ['get', 'post'],
        'edit'    => ['get' => '{id}'],
        'update'  => ['put' => '{id}'],
        'destroy' => ['delete' => '{id}'],
    ],
    'card'                   => $default,
    'class'                  => $default,
    'combo_type'             => $default,
    'company'                => $default,
    'consumption'            => [
        'index'  => ['get'],
        'show'   => ['get'],
        'stat'   => ['post'],
        'export' => ['get'],
    ],
    'conference_room'        => $default,
    'conference_queue'       => $default,
    'conference_participant' => [
        'index' => ['get'],
        'store' => ['post'],
        'show'  => ['get' => '{id}'],
    ],
    'corp'                   => $default,
    'custodian'              => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['get' => '{id}', 'post' => '{id}'],
        'update'  => ['put' => '{id?}'],
        'destroy' => ['delete' => '{id?}'],
        'issue'   => ['get', 'post'],
        'permit'  => ['get', 'post'],
    ],
    'department'             => [
        'index'   => ['get', 'post'],
        'create'  => ['get' => '{parentId}'],
        'store'   => ['post'],
        'edit'    => ['get' => '{id}'],
        'update'  => ['put' => '{id}'],
        'destroy' => ['delete' => '{id}'],
    ],
    'educator'               => [
        'index'    => ['get'],
        'create'   => ['get', 'post'],
        'store'    => ['post'],
        'edit'     => ['get' => '{id}', 'post' => '{id}'],
        'update'   => ['put' => '{id?}'],
        'recharge' => ['get' => '{id}', 'put' => '{id}'],
        'destroy'  => ['delete' => 'id?'],
        'import'   => ['post'],
        'export'   => ['get', 'post'],
        'issue'    => ['get', 'post'],
        'permit'   => ['get', 'post'],
    ],
    'event'                  => $default,
    'exam'                   => $default,
    'exam_type'              => $default,
    'grade'                  => $default,
    'group'                  => [
        'index'  => ['get'],
        'create' => ['get', 'post'],
        'edit'   => ['get' => '{id}', 'post' => '{id}'],
    ],
    'icon'                   => $default,
    'init'                   => [
        'index' => ['get', 'post'],
    ],
    'major'                  => $default,
    'menu'                   => [
        'index'   => ['get', 'post'],
        'create'  => ['get' => '{parentId}'],
        'store'   => ['post'],
        'edit'    => ['get' => '{id}'],
        'update'  => ['put' => '{id}'],
        'destroy' => ['delete' => '{id}'],
        'sort'    => ['get' => '{id}', 'post' => '{id}'],
    ],
    'message'                => [
        'index'   => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['get' => '{id}'],
        'update'  => ['put' => '{id?}'],
        'show'    => ['get' => '{id}'],
        'send'    => ['post'],
        'destroy' => ['delete' => '{id?}'],
    ],
    'module'                 => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['get' => '{id}', 'post' => '{id}'],
        'update'  => ['get' => '{id?}'],
        'destroy' => ['delete' => '{id?}'],
    ],
    'operator'               => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['get' => '{id}', 'post' => '{id}'],
        'update'  => ['get' => '{id?}'],
        'destroy' => ['delete' => '{id?}'],
    ],
    'partner'                => $default,
    'passage_log'            => [
        'index'  => ['get'],
        'store'  => ['post'],
        'export' => ['post'],
    ],
    'passage_rule'           => array_merge(
        $default,
        ['issue' => ['post']]
    ),
    'school'                 => $default,
    'score'                  => [
        'index'   => ['get'],
        'create'  => ['get' => '{examId?}'],
        'edit'    => ['get' => '{id}/{examId?}'],
        'store'   => ['post'],
        'update'  => ['put' => '{id?}'],
        'destroy' => ['delete' => '{id?}'],
        'rank'    => ['get' => '{examId}'],
        'stat'    => ['get', 'post'],
        'import'  => ['get' => '{examId?}', 'post' => '{examId?}'],
        'export'  => ['get' => '{examId?}', 'post' => '{examId?}'],
        'send'    => ['post'],
    ],
    'score_range'            => array_merge(
        $default,
        ['stat' => ['get', 'post']]
    ),
    'score_total'            => [
        'index' => ['get'],
        'stat'  => ['get' => '{examId}'],
    ],
    'semester'               => $default,
    'student'                => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['get' => '{id}', 'post' => '{id}'],
        'update'  => ['put' => '{id?}'],
        'destroy' => ['delete' => '{id?}'],
        'import'  => ['post'],
        'export'  => ['get', 'post'],
        'issue'   => ['get', 'post'],
        'permit'  => ['get', 'post'],
    ],
    'subject'                => $default,
    'subject_module'         => $default,
    'tab'                    => [
        'index'  => ['get'],
        'edit'   => ['get' => '{id}'],
        'update' => ['put' => '{id}'],
    ],
    'tag'                    => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['get' => '{id?}', 'post'],
        'update'  => ['get' => '{id?}'],
        'destroy' => ['delete' => '{id?}'],
    ],
    'turnstile'              => [
        'index' => ['get'],
        'store' => ['post'],
    ],
    'user'                   => [
        'edit'    => ['get'],
        'update'  => ['put'],
        'reset'   => ['get', 'post'],
        'message' => ['get'],
        'event'   => ['get'],
    ],
    'wap_site'               => [
        'index'  => ['get'],
        'edit'   => ['get' => '{id}', 'post' => '{id}'],
        'update' => ['put' => '{id}'],
    ],
    'wap_site_module'        => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['get' => '{id}', 'post' => '{id}'],
        'update'  => ['put' => '{id}'],
        'destroy' => ['delete' => '{id}'],
    ],
    'wsm_article'            => [
        'index'   => ['get'],
        'create'  => ['get', 'post'],
        'store'   => ['post'],
        'edit'    => ['get' => '{id}', 'post' => '{id}'],
        'update'  => ['put' => '{id}'],
        'destroy' => ['delete' => '{id}'],
    ],
    # 演示(微信端)
    'demo'                   => [
        'index'     => ['get'],
        'safe'      => ['get'],
        'wisdom'    => ['get'],
        'classroom' => ['get'],
        'info'      => ['get'],
    ],
];
foreach ($routes as $model => $methods) {
    $table = Inflector::pluralize($model);
    $model = $model == 'class' ? 'squad' : $model;
    $controller = ucfirst(Inflector::camelize($model)) . 'Controller';
    foreach ($methods as $method => $actions) {
        foreach ($actions as $key => $value) {
            $paths = [$table, $method == 'destroy' ? 'delete' : $method];
            $action = is_numeric($key) ? $value : $key;
            is_numeric($key) ?: $paths = array_merge($paths, [$value]);
            Route::$action(
                implode('/', $paths),
                implode('@', [$controller, $method])
            );
        }
    }
}
/** 微信端路由 -------------------------------------------------------------------------------------------------------- */
Route::get('sms/{urlcode}', 'Wechat\WechatSmsController@show');    # 未关注的用户查看微信消息详情的链接
$acronyms = Corp::pluck('acronym')->toArray();
foreach ($acronyms as $acronym) {
    app_routes($acronym);
}