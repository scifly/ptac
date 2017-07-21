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

// 科目设置
Route::get('subjects/index', 'SubjectController@index');
Route::get('subjects/create', 'SubjectController@create');
Route::post('subjects', 'SubjectController@store');
Route::get('subjects/{subject}', 'SubjectController@show');
Route::get('subjects/{subject}/edit', 'SubjectController@edit');
Route::put('subjects/{subject}', 'SubjectController@update');
Route::delete('subjects/{subject}', 'SubjectController@destroy');

// 权限设置
Route::get('groups/index', 'GroupController@index');
Route::get('groups/create', 'GroupController@create');
Route::post('groups', 'GroupController@store');
Route::get('groups/{groups}', 'GroupController@show');
Route::get('groups/{groups}/edit', 'GroupController@edit');
Route::put('groups/{groups}', 'GroupController@update');
Route::delete('groups/{groups}', 'GroupController@destroy');

// 学生设置
Route::get('students/index', 'StudentController@index');
Route::get('students/create', 'StudentController@create');
Route::post('students', 'StudentController@store');
Route::get('students/{students}', 'StudentController@show');
Route::get('students/{students}/edit', 'StudentController@edit');
Route::put('students/{students}', 'StudentController@update');
Route::delete('students/{students}', 'StudentController@destroy');