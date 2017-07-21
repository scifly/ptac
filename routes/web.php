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
Route::post('schools/create', 'SchoolController@store');
Route::get('schools/show/{id}', 'SchoolController@show');
Route::get('schools/edit/{id}', 'SchoolController@edit');
Route::put('schools/edit/{id}', 'SchoolController@update');
Route::delete('schools/delete/{id}', 'SchoolController@destroy');

// 运营者公司设置
Route::get('companies/index', 'CompanyController@index');
Route::get('companies/create', 'CompanyController@create');
Route::post('companies', 'CompanyController@store');
Route::get('companies/{company}', 'CompanyController@show');
Route::get('companies/{company}/edit', 'CompanyController@edit');
Route::put('companies/{company}', 'CompanyController@update');
Route::delete('companies/{company}', 'CompanyController@destroy');

//年级班级设置
Route::get('grades/index', 'GradeController@index');
Route::get('grades/create', 'GradeController@create');
Route::post('grades', 'GradeController@index');
Route::get('grades/{grade}', 'GradeController@show');
Route::get('grades/{grade}/edit', 'GradeController@edit');
Route::put('grades/{grade}', 'GradeController@update');
Route::delete('grades/{grade}', 'GradeController@destroy');