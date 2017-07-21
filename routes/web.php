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
Route::post('schools/store', 'SchoolController@store');
Route::get('schools/show/{id}', 'SchoolController@show');
Route::get('schools/edit/{id}', 'SchoolController@edit');
Route::put('schools/update/{id}', 'SchoolController@update');
Route::delete('schools/delete/{id}', 'SchoolController@destroy');

// 科目设置
Route::get('subjects/index', 'SubjectController@index');
Route::get('subjects/create', 'SubjectController@create');
Route::post('subjects/store', 'SubjectController@store');
Route::get('subjects/show/{id}', 'SubjectController@show');
Route::get('subjects/edit/{id}', 'SubjectController@edit');
Route::put('subjects/update/{id}', 'SubjectController@update');
Route::delete('subjects/delete{id}', 'SubjectController@destroy');

// 权限设置
Route::get('groups/index', 'GroupController@index');
Route::get('groups/create', 'GroupController@create');
Route::post('groups/store', 'GroupController@store');
Route::get('groups/show/{id}', 'GroupController@show');
Route::get('groups/edit/{id}', 'GroupController@edit');
Route::put('groups/update/{id}', 'GroupController@update');
Route::delete('groups/delete/{id}', 'GroupController@destroy');

// 学生设置
Route::get('students/index', 'StudentController@index');
Route::get('students/create', 'StudentController@create');
Route::post('students/store', 'StudentController@store');
Route::get('students/show/{id}', 'StudentController@show');
Route::get('students/edit/{id}', 'StudentController@edit');
Route::put('students/update/{id}', 'StudentController@update');
Route::delete('students/delete/{id}', 'StudentController@destroy');