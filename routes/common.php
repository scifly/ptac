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
        Route::get('create/{id?}', $ctlr . '@create');
        Route::post('store', $ctlr . '@store');
        Route::get('show/{id}', $ctlr . '@show');
        Route::get('edit/{id}', $ctlr . '@edit');
        Route::put('update/{id}', $ctlr . '@update');
        Route::delete('delete/{id}', $ctlr . '@destroy');
        Route::get('userInfo', $ctlr . '@getUserInfo');
    };
}