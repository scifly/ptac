<?php

use Illuminate\Support\Facades\Route;

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
Route::get('subjects/index', 'SubjectController@index');
Route::get('subjects/create', 'SubjectController@create');
Route::post('subjects/store', 'SubjectController@store');
Route::get('subjects/show/{id}', 'SubjectController@show');
Route::get('subjects/edit/{id}', 'SubjectController@edit');
Route::put('subjects/update/{id}', 'SubjectController@update');
Route::delete('subjects/delete/{id}', 'SubjectController@destroy');
Route::get('subjects/query/{id}', 'SubjectController@query');

// 科目次分类
Route::get('subjectModules/index', 'SubjectModulesController@index');
Route::get('subjectModules/create', 'SubjectModulesController@create');
Route::post('subjectModules/store', 'SubjectModulesController@store');
Route::get('subjectModules/show/{id}', 'SubjectModulesController@show');
Route::get('subjectModules/edit/{id}', 'SubjectModulesController@edit');
Route::put('subjectModules/update/{id}', 'SubjectModulesController@update');
Route::delete('subjectModules/delete/{id}', 'SubjectModulesController@destroy');

// 权限设置
Route::get('groups/index', 'GroupController@index');
Route::get('groups/create', 'GroupController@create');
Route::post('groups/store', 'GroupController@store');
Route::get('groups/show/{id}', 'GroupController@show');
Route::get('groups/edit/{id}', 'GroupController@edit');
Route::put('groups/update/{id}', 'GroupController@update');
Route::delete('groups/delete/{id}', 'GroupController@destroy');

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

Route::get('educatorClasses/index', 'EducatorClassController@index');
Route::get('educatorClasses/create', 'EducatorClassController@create');
Route::post('educatorClasses/store', 'EducatorClassController@store');
Route::get('educatorClasses/show/{id}', 'EducatorClassController@show');
Route::get('educatorClasses/edit/{id}', 'EducatorClassController@edit');
Route::put('educatorClasses/update/{id}', 'EducatorClassController@update');
Route::delete('educatorClasses/delete/{id}', 'EducatorClassController@destroy');

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

// 成绩统计项设置
Route::get('score_ranges/index', 'ScoreRangeController@index');
Route::get('score_ranges/create', 'ScoreRangeController@create');
Route::post('score_ranges/store', 'ScoreRangeController@store');
Route::get('score_ranges/show/{id}', 'ScoreRangeController@show');
Route::get('score_ranges/edit/{id}', 'ScoreRangeController@edit');
Route::put('score_ranges/update/{id}', 'ScoreRangeController@update');
Route::delete('score_ranges/delete/{id}', 'ScoreRangeController@destroy');
# 考勤管理
//考勤机设置
Route::get('attendance_machines/index', 'AttendanceMachineController@index');
Route::get('attendance_machines/create', 'AttendanceMachineController@create');
Route::post('attendance_machines/store', 'AttendanceMachineController@store');
Route::get('attendance_machines/show/{id}', 'AttendanceMachineController@show');
Route::get('attendance_machines/edit/{id}', 'AttendanceMachineController@edit');
Route::put('attendance_machines/update/{id}', 'AttendanceMachineController@update');
Route::delete('attendance_machines/delete/{id}', 'AttendanceMachineController@destroy');


//流程类型设置
Route::get('procedure_types/index', 'ProcedureTypeController@index');
Route::get('procedure_types/create', 'ProcedureTypeController@create');
Route::post('procedure_types/store', 'ProcedureTypeController@store');
Route::get('procedure_types/show/{id}', 'ProcedureTypeController@show');
Route::get('procedure_types/edit/{id}', 'ProcedureTypeController@edit');
Route::put('procedure_types/update/{id}', 'ProcedureTypeController@update');
Route::delete('procedure_types/delete/{id}', 'ProcedureTypeController@destroy');

//流程设置
Route::get('procedures/index', 'ProcedureController@index');
Route::get('procedures/create', 'ProcedureController@create');
Route::post('procedures/store', 'ProcedureController@store');
Route::get('procedures/show/{id}', 'ProcedureController@show');
Route::get('procedures/edit/{id}', 'ProcedureController@edit');
Route::put('procedures/update/{id}', 'ProcedureController@update');
Route::delete('procedures/delete/{id}', 'ProcedureController@destroy');

//流程步骤设置
Route::get('procedure_steps/index', 'ProcedureStepController@index');
Route::get('procedure_steps/create', 'ProcedureStepController@create');
Route::post('procedure_steps/store', 'ProcedureStepController@store');
Route::get('procedure_steps/show/{id}', 'ProcedureStepController@show');
Route::get('procedure_steps/edit/{id}', 'ProcedureStepController@edit');
Route::put('procedure_steps/update/{id}', 'ProcedureStepController@update');
Route::delete('procedure_steps/delete/{id}', 'ProcedureStepController@destroy');

//流程日志
Route::get('procedure_logs/index', 'ProcedureLogController@index');
Route::get('procedure_logs/show/{id}', 'ProcedureLogController@show');
Route::delete('procedure_logs/delete/{id}', 'ProcedureLogController@destroy');