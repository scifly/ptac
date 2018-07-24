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
        Route::get('userInfo', $ctlr . '@getUserInfo');
    };
}

/**
 * routes - Helper function
 * 返回MenuController,DepartmentController resource路由
 *
 * @param $ctlr
 * @return Closure
 */
function routeItem($ctlr) {
    return function () use ($ctlr) {
        Route::get('index', $ctlr . '@index');
        Route::get('create/{id?}', $ctlr . '@create');
        Route::post('store', $ctlr . '@store');
        Route::get('edit/{id}', $ctlr . '@edit');
        Route::put('update/{id}', $ctlr . '@update');
        Route::delete('delete/{id}', $ctlr . '@destroy');
        Route::get('userInfo', $ctlr . '@getUserInfo');
    };
}

/**
 * 微信端应用路由
 *
 * @param $acronym - 企业微信名称首字母缩略词
 */
function app_routes($acronym) {
    
    Route::get($acronym . '/schools', 'HomeController@wIndex');
    Route::get($acronym . '/sync', 'Wechat\SyncController@sync');
    Route::post($acronym . '/sync', 'Wechat\SyncController@sync');
    
    /** 消息中心 */
    $c = 'Wechat\MessageCenterController';
    $p = $acronym . '/mc/';
    Route::get($p, $c . '@index');
    Route::post($p, $c . '@index');
    Route::get($p . 'create', $c . '@create');
    Route::post($p . 'create', $c . '@create');
    Route::post($p . 'send', $c . '@send');
    Route::post($p . 'store', $c . '@store');
    Route::put($p . 'update/{id?}', $c . '@update');
    Route::get($p . 'show/{id}', $c . '@show');
    Route::get($p . 'read/{id}', $c . '@read');
    Route::delete($p . 'delete/{id}', $c . '@destroy');
    Route::post($p . 'upload', $c . '@upload');
    Route::post($p . 'reply', $c . '@reply');
    Route::post($p . 'replies', $c . '@replies');
    Route::delete($p . 'remove/{id}', $c . '@remove');
    
    /** 考勤中心 */
    $c = 'Wechat\AttendanceController';
    $p = $acronym . '/at/';
    Route::get($p, $c . '@index');
    Route::get($p . 'detail/{id}', $c . '@detail');
    Route::post($p . 'detail/{id?}', $c . '@detail');
    Route::post($p . 'chart', $c . '@chart');
    
    /** 成绩中心 */
    $c = 'Wechat\ScoreCenterController';
    $p = $acronym . '/sc/';
    Route::any($p, $c . '@index');
    Route::get($p . 'detail', $c . '@detail');
    Route::post($p . 'detail', $c . '@detail');
    Route::any($p . 'show', $c . '@show');
    Route::get($p . 'analyze', $c . '@analyze');
    Route::get($p . 'stat', $c . '@stat');
    
    /** 布置作业 */
    Route::get($acronym . '/homework', 'Wechat\HomeWorkController@index');
    
    /** 微网站 */
    $c = 'Wechat\MobileSiteController';
    $p = $acronym . '/ws/';
    Route::any($p, $c . '@index');
    Route::any($p . 'module', $c . '@module');
    Route::any($p . 'article', $c . '@article');
}