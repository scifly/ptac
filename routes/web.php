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

# Auth::routes();
# Route::get('/', function() { return 'Dashboard'; });
# Route::get('/', 'HomeController@index');
# Route::get('/home', 'HomeController@index')->name('home');
# Route::get('test/index', 'TestController@index');
# Route::get('/', 'HomeController@index');

# 系统设置
// 学校设置
Route::get('schools/index', 'SchoolController@index');
Route::get('schools/create', 'SchoolController@create');
Route::post('schools/store', 'SchoolController@store');
Route::get('schools/show/{id}', 'SchoolController@show');
Route::get('schools/edit/{id}', 'SchoolController@edit');
Route::put('schools/update/{id}', 'SchoolController@update');
Route::delete('schools/delete/{id}', 'SchoolController@destroy');

// 学校类型设置
Route::get('school_types/index', 'SchoolTypeController@index');
Route::get('school_types/create', 'SchoolTypeController@create');
Route::post('school_types/store', 'SchoolTypeController@store');
Route::get('school_types/show/{id}', 'SchoolTypeController@show');
Route::get('school_types/edit/{id}', 'SchoolTypeController@edit');
Route::put('school_types/update/{id}', 'SchoolTypeController@update');
Route::delete('school_types/delete/{id}', 'SchoolTypeController@destroy');

// 科目设置
Route::get('subject/index', 'SubjectController@index');
Route::get('subject/create', 'SubjectController@create');
Route::post('subject/store', 'SubjectController@store');
Route::get('subject/show/{id}', 'SubjectController@show');
Route::get('subject/edit/{id}', 'SubjectController@edit');
Route::put('subject/update/{id}', 'SubjectController@update');

// 权限设置
Route::get('group/index', 'GroupController@index');
Route::get('group/create', 'GroupController@create');
Route::post('group/store', 'GroupController@store');
Route::get('group/show/{id}', 'GroupController@show');
Route::get('group/edit/{id}', 'GroupController@edit');
Route::put('group/update/{id}', 'GroupController@update');

// 运营者公司设置
Route::get('companies/index', 'CompanyController@index');
Route::get('companies/create', 'CompanyController@create');
Route::post('companies/store', 'CompanyController@store');
Route::get('companies/show/{id}', 'CompanyController@show');
Route::get('companies/edit/{id}', 'CompanyController@edit');
Route::put('companies/update/{id}', 'CompanyController@update');
Route::delete('companies/delete/{id}', 'CompanyController@destroy');

//年级班级设置
Route::get('grades/index', 'GradeController@index');
Route::get('grades/create', 'GradeController@create');
Route::post('grades/store', 'GradeController@store');
Route::get('grades/show/{id}', 'GradeController@show');
Route::get('grades/edit/{id}', 'GradeController@edit');
Route::put('grades/update/{id}', 'GradeController@update');
Route::delete('grades/delete/{id}', 'GradeController@destroy');

Route::get('classes/index', 'SquadController@index');
Route::get('classes/create', 'SquadController@create');
Route::post('classes/store', 'SquadController@store');
Route::get('classes/show/{id}', 'SquadController@show');
Route::get('classes/edit/{id}', 'SquadController@edit');
Route::put('classes/update/{id}', 'SquadController@update');
Route::delete('classes/delete/{id}', 'SquadController@destroy');

// 应用设置
Route::get('apps/index', 'AppController@index');
Route::get('apps/create', 'AppController@create');
Route::post('apps/store', 'AppController@store');
Route::get('apps/show/{id}', 'AppController@show');
Route::get('apps/edit/{id}', 'AppController@edit');
Route::put('apps/update/{id}', 'AppController@update');
Route::delete('apps/delete/{id}', 'AppController@destroy');

// 用户/通信管理
// 教职员工
Route::get('educators/index', 'EducatorController@index');
Route::get('educators/create', 'EducatorController@create');
Route::post('educators/store', 'EducatorController@store');
Route::get('educators/show/{id}', 'EducatorController@show');
Route::get('educators/edit/{id}', 'EducatorController@edit');
Route::put('educators/update/{id}', 'EducatorController@update');
Route::delete('educators/delete/{id}', 'EducatorController@destroy');

// 学生设置
Route::get('students/index', 'StudentController@index');
Route::get('students/create', 'StudentController@create');
Route::post('students/store', 'StudentController@store');
Route::get('students/show/{id}', 'StudentController@show');
Route::get('students/edit/{id}', 'StudentController@edit');
Route::put('students/update/{id}', 'StudentController@update');
Route::delete('students/delete/{id}', 'StudentController@destroy');

// 企业设置
Route::get('corps/index', 'CorpController@index');
Route::get('corps/create', 'CorpController@create');
Route::post('corps/store', 'CorpController@store');
Route::get('corps/show/{id}', 'CorpController@show');
Route::get('corps/edit/{id}', 'CorpController@edit');
Route::put('corps/update/{id}', 'CorpController@update');
Route::delete('corps/delete/{id}', 'CorpController@destroy');

# 成绩管理
//成绩管理
Route::get('scores/index', 'ScoreController@index');
Route::get('scores/create', 'ScoreController@create');
Route::post('scores/store', 'ScoreController@store');
Route::get('scores/show/{id}', 'ScoreController@show');
Route::get('scores/edit/{id}', 'ScoreController@edit');
Route::put('scores/update/{id}', 'ScoreController@update');
Route::delete('scores/delete/{id}', 'ScoreController@destroy');

# 考勤管理
//考勤机设置
Route::get('attendance_machines/index', 'AttendanceMachineController@index');
Route::get('attendance_machines/create', 'AttendanceMachineController@create');
Route::post('attendance_machines/store', 'AttendanceMachineController@store');
Route::get('attendance_machines/show/{id}', 'AttendanceMachineController@show');
Route::get('attendance_machines/edit/{id}', 'AttendanceMachineController@edit');
Route::put('attendance_machines/update/{id}', 'AttendanceMachineController@update');
Route::delete('attendance_machines/delete/{id}', 'AttendanceMachineController@destroy');