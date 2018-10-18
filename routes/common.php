<?php
/**
 * routes - Helper function
 * 返回resource路由
 *
 * @param $ctlr
 * @return Closure
 */
function routes($ctlr) {
    return function () use ($ctlr) {
        Route::get('index', $ctlr . '@index');
        Route::get('create', $ctlr . '@create');
        Route::post('store', $ctlr . '@store');
        Route::get('edit/{id}', $ctlr . '@edit');
        Route::put('update/{id?}', $ctlr . '@update');
        Route::delete('delete/{id?}', $ctlr . '@destroy');
    };
}

/**
 * 微信端应用路由
 *
 * @param $acronym - 企业微信名称首字母缩略词
 */
function app_routes($acronym) {
    
    Route::get($acronym . '/schools', 'Wechat\WechatController@schools');
    Route::get($acronym . '/sync', 'Wechat\SyncController@sync');
    Route::post($acronym . '/sync', 'Wechat\SyncController@sync');
    
    /** 消息中心 */
    $c = 'Wechat\MessageCenterController';
    $p = $acronym . '/message_centers/';
    Route::get($p, $c . '@index');
    Route::post($p, $c . '@index');
    Route::get($p . 'create', $c . '@create');
    Route::post($p . 'create', $c . '@create');
    Route::post($p . 'store', $c . '@store');
    Route::get($p . 'edit/{id?}', $c . '@edit');
    Route::post($p . 'edit/{id?}', $c . '@edit');
    Route::put($p . 'update/{id?}', $c . '@update');
    Route::get($p . 'show/{id}', $c . '@show');
    Route::post($p . 'show/{id?}', $c . '@show');
    Route::delete($p . 'show/{id?}', $c . '@show');
    Route::delete($p . 'delete/{id}', $c . '@destroy');
    Route::post($p . 'send', $c . '@send');
    
    /** 考勤中心 */
    $c = 'Wechat\AttendanceController';
    $p = $acronym . '/attendances/';
    Route::get($p, $c . '@index');
    Route::get($p . 'detail/{id}', $c . '@detail');
    Route::post($p . 'detail/{id?}', $c . '@detail');
    Route::post($p . 'chart', $c . '@chart');
    
    /** 成绩中心 */
    $c = 'Wechat\ScoreCenterController';
    $p = $acronym . '/score_centers/';
    Route::get($p, $c . '@index');
    Route::post($p, $c . '@index');
    Route::get($p . 'detail', $c . '@detail');
    Route::post($p . 'detail', $c . '@detail');
    Route::get($p . 'graph', $c . '@graph');
    Route::post($p . 'graph', $c . '@graph');
    Route::get($p . 'analyze', $c . '@analyze');
    Route::get($p . 'stat', $c . '@stat');
    
    /** 布置作业 */
    $c = 'Wechat\HomeWorkController';
    $p = $acronym . '/home_works/';
    Route::get($p, $c . '@index');
    
    /** 微网站 */
    $c = 'Wechat\MobileSiteController';
    $p = $acronym . '/mobile_sites/';
    Route::get($p, $c . '@index');
    Route::get($p . 'module', $c . '@module');
    Route::get($p . 'article', $c . '@article');
}