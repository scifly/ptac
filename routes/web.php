<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//Auth::routes();
//Route::get('/home', 'HomeController@index')->name('home');


//Route::get('/', function() { return 'Dashboard'; });

Route::get('/', 'HomeController@index');


Route::get('schools/types/{name}', function($name) {
    $schoolType = App\Models\SchoolType::with('schools')
        ->whereName($name)
        ->first();
    return view('admin.config.school.schools_index')
        ->with('schoolType', $schoolType)
        ->with('schools', $schoolType->schools);
});

# 系统设置

// 学校设置
Route::get('schools/index', 'SchoolController@index');
Route::get('schools/create', 'SchoolController@create');
Route::post('schools', 'SchoolController@store');
Route::get('schools/{school}', 'SchoolController@show');
Route::get('schools/{school}/edit', 'SchoolController@edit');
Route::put('schools/{school}', 'SchoolController@update');
Route::delete('schools/{school}', 'SchoolController@destroy');

//年级班级设置
Route::get('grades/index', 'GradeController@index');
Route::get('grades/create', 'GradeController@create');
Route::post('grades', 'GradeController@index');
Route::get('grades/{grade}', 'GradeController@show');
Route::get('grades/{grade}/edit', 'GradeController@edit');
Route::put('grades/{grade}', 'GradeController@update');
Route::delete('grades/{grade}', 'GradeController@destroy');

Route::get('classes/index', 'SquadController@index');
Route::get('classes/create', 'SquadController@create');
Route::post('classes', 'SquadController@index');
Route::get('classes/{squad}', 'SquadController@show');
Route::get('classes/{squad}/edit', 'SquadController@edit');
Route::put('classes/{squad}', 'SquadController@update');
Route::delete('classes/{squad}', 'SquadController@destroy');

//用户/通信管理
//教职员工

Route::get('educators/index', 'EducatorController@index');
Route::get('educators/create', 'EducatorController@create');
Route::post('educators', 'EducatorController@index');
Route::get('educators/{educator}', 'EducatorController@show');
Route::get('educators/{educator}/edit', 'EducatorController@edit');
Route::put('educators/{educator}', 'EducatorController@update');
Route::delete('educators/{educator}', 'EducatorController@destroy');




Route::get('test/index', 'TestController@index');


