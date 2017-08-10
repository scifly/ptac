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

# 菜单管理
// action设置
Route::get('actions/index', 'ActionController@index');
Route::get('actions/create', 'ActionController@create');
Route::post('actions/store', 'ActionController@store');
Route::get('actions/show/{id}', 'ActionController@show');
Route::get('actions/edit/{id}', 'ActionController@edit');
Route::put('actions/update/{id}', 'ActionController@update');
Route::delete('actions/delete/{id}', 'ActionController@destroy');


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
Route::get('subject_modules/index', 'SubjectModuleController@index');
Route::get('subject_modules/create', 'SubjectModuleController@create');
Route::post('subject_modules/store', 'SubjectModuleController@store');
Route::get('subject_modules/show/{id}', 'SubjectModuleController@show');
Route::get('subject_modules/edit/{id}', 'SubjectModuleController@edit');
Route::put('subject_modules/update/{id}', 'SubjectModuleController@update');
Route::delete('subject_modules/delete/{id}', 'SubjectModuleController@destroy');


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

// 教职员工-班级
Route::get('educators_classes/index', 'EducatorClassController@index');
Route::get('educators_classes/create', 'EducatorClassController@create');
Route::post('educators_classes/store', 'EducatorClassController@store');
Route::get('educators_classes/show/{id}', 'EducatorClassController@show');
Route::get('educators_classes/edit/{id}', 'EducatorClassController@edit');
Route::put('educators_classes/update/{id}', 'EducatorClassController@update');
Route::delete('educators_classes/delete/{id}', 'EducatorClassController@destroy');

//监护人-学生
Route::get('custodians_students/index', 'CustodianStudentController@index');
Route::get('custodians_students/create', 'CustodianStudentController@create');
Route::post('custodians_students/store', 'CustodianStudentController@store');
Route::get('custodians_students/show/{id}', 'CustodianStudentController@show');
Route::get('custodians_students/edit/{id}', 'CustodianStudentController@edit');
Route::put('custodians_students/update/{id}', 'CustodianStudentController@update');
Route::delete('custodians_students/delete/{id}', 'CustodianStudentController@destroy');

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
Route::get('scores/statistics/{exam_id}', 'ScoreController@statistics');

// 总成绩设置
Route::get('score_totals/statistics/{exam_id}', 'ScoreTotalController@statistics');
Route::get('score_totals/index', 'ScoreTotalController@index');
Route::get('score_totals/create', 'ScoreTotalController@create');
Route::post('score_totals/store', 'ScoreTotalController@store');
Route::get('score_totals/show/{id}', 'ScoreTotalController@show');
Route::get('score_totals/edit/{id}', 'ScoreTotalController@edit');
Route::put('score_totals/update/{id}', 'ScoreTotalController@update');
Route::delete('score_totals/delete/{id}', 'ScoreTotalController@destroy');
Route::get('score_totals/getExamSubjects/{id}', 'ScoreTotalController@getExamSubjects');

// 成绩统计项设置
Route::get('score_ranges/statistics_show', 'ScoreRangeController@statisticsShow');
Route::get('score_ranges/statistics', 'ScoreRangeController@statistics');
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

//流程步骤设置
Route::get('procedure_steps/index', 'ProcedureStepController@index');
Route::get('procedure_steps/create', 'ProcedureStepController@create');
Route::post('procedure_steps/store', 'ProcedureStepController@store');
Route::get('procedure_steps/show/{id}', 'ProcedureStepController@show');
Route::get('procedure_steps/edit/{id}', 'ProcedureStepController@edit');
Route::put('procedure_steps/update/{id}', 'ProcedureStepController@update');
Route::get('procedure_steps/delete/{id}', 'ProcedureStepController@destroy');
Route::get('procedure_steps/getSchoolEducators/{id}', 'ProcedureStepController@getSchoolEducators');

//流程日志
Route::get('procedure_logs/index', 'ProcedureLogController@index');
Route::get('procedure_logs/show/{id}', 'ProcedureLogController@show');

//用户管理-用户设置
Route::get('users/index', 'UserController@index');
Route::get('users/create', 'UserController@create');
Route::post('users/store', 'UserController@store');
Route::get('users/show/{id}', 'UserController@show');
Route::get('users/edit/{id}', 'UserController@edit');
Route::put('users/update/{id}', 'UserController@update');
Route::delete('users/delete/{id}', 'UserController@destroy');
Route::any('users/uploadavatar', 'UserController@uploadAvatar');
Route::any('users/delavatar', 'UserController@delAvatar');

//考试类型设置
Route::get('exam_types/index', 'ExamTypeController@index');
Route::get('exam_types/create', 'ExamTypeController@create');
Route::post('exam_types/store', 'ExamTypeController@store');
Route::get('exam_types/show/{id}', 'ExamTypeController@show');
Route::get('exam_types/edit/{id}', 'ExamTypeController@edit');
Route::put('exam_types/update/{id}', 'ExamTypeController@update');
Route::delete('exam_types/delete/{id}', 'ExamTypeController@destroy');

//考试设置
Route::get('exams/index', 'ExamController@index');
Route::get('exams/create', 'ExamController@create');
Route::post('exams/store', 'ExamController@store');
Route::get('exams/show/{id}', 'ExamController@show');
Route::get('exams/edit/{id}', 'ExamController@edit');
Route::put('exams/update/{id}', 'ExamController@update');
Route::delete('exams/delete/{id}', 'ExamController@destroy');

#问卷调查参与
Route::group(['prefix' => 'pollQuestionnaireParticpation'],function(){
    Route::get('/', 'PqParticipantController@index');
    Route::get('/index', 'PqParticipantController@index');
    Route::post('/show/{id}', 'PqParticipantController@show');
    Route::put('/update', 'PqParticipantController@update')->name("pqp_update");

});

#成绩发送
Route::group(['prefix' => 'scoreSend'],function(){
    Route::get('/', 'Score_SendController@index');
    Route::get('/index', 'Score_SendController@index@index');
    Route::Post('/getgrade/{id}', 'Score_SendController@getGrade');
    Route::Post('/getclass/{id}', 'Score_SendController@getClass');
    Route::Post('/getexam/{id}', 'Score_SendController@getExam');
    Route::Post('/getsubject/{id}', 'Score_SendController@getSubject');
    Route::post('/preview/{examId}/{classId}/{subjectIds}/{itemId}', 'Score_SendController@preview');
});


//微网站管理
Route::get('wapsites/index', 'WapSiteController@index');
Route::get('wapsites/create', 'WapSiteController@create');
Route::post('wapsites/store', 'WapSiteController@store');
Route::get('wapsites/show/{id}', 'WapSiteController@show');
Route::get('wapsites/edit/{id}', 'WapSiteController@edit');
Route::put('wapsites/update/{id}', 'WapSiteController@update');
Route::delete('wapsites/delete/{id}', 'WapSiteController@destroy');
Route::any('wapsites/uploadwapsite', 'WapSiteController@uploadImages');
Route::any('wapsites/delimage', 'WapSiteController@delImage');

//微网站管理-网站模块管理
Route::get('wapsitemodules/index', 'WapSiteModuleController@index');
Route::get('wapsitemodules/create', 'WapSiteModuleController@create');
Route::post('wapsitemodules/store', 'WapSiteModuleController@store');
Route::get('wapsitemodules/show/{id}', 'WapSiteModuleController@show');
Route::get('wapsitemodules/edit/{id}', 'WapSiteModuleController@edit');
Route::put('wapsitemodules/update/{id}', 'WapSiteModuleController@update');
Route::delete('wapsitemodules/delete/{id}', 'WapSiteModuleController@destroy');

//微网站管理-文章管理
Route::get('wsmarticles/index', 'WsmArticleController@index');
Route::get('wsmarticles/create', 'WsmArticleController@create');
Route::post('wsmarticles/store', 'WsmArticleController@store');
Route::get('wsmarticles/show/{id}', 'WsmArticleController@show');
Route::get('wsmarticles/edit/{id}', 'WsmArticleController@edit');
Route::put('wsmarticles/update/{id}', 'WsmArticleController@update');
Route::delete('wsmarticles/delete/{id}', 'WsmArticleController@destroy');

