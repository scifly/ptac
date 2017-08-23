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
Route::get('test/index', 'TestController@index');
Route::get('test/create', 'TestController@create');
Route::get('pages/{id}', 'HomeController@menu');

# 菜单管理
// action设置
Route::get('actions/index', 'ActionController@index');
Route::get('actions/create', 'ActionController@create');
Route::post('actions/store', 'ActionController@store');
Route::get('actions/show/{id}', 'ActionController@show');
Route::get('actions/edit/{id}', 'ActionController@edit');
Route::put('actions/update/{id}', 'ActionController@update');
Route::delete('actions/delete/{id}', 'ActionController@destroy');
// 卡片管理
Route::get('tabs/index', 'TabController@index');
Route::get('tabs/create', 'TabController@create');
Route::post('tabs/store', 'TabController@store');
Route::get('tabs/show/{id}', 'TabController@show');
Route::get('tabs/edit/{id}', 'TabController@edit');
Route::put('tabs/update/{id}', 'TabController@update');
Route::delete('tabs/delete/{id}', 'TabController@destroy');
// 菜单设置
Route::get('menus/index', 'MenuController@index');
Route::post('menus/index', 'MenuController@index');
Route::get('menus/create', 'MenuController@create');
Route::post('menus/store', 'MenuController@store');
Route::get('menus/show/{id}', 'MenuController@show');
Route::get('menus/edit/{id}', 'MenuController@edit');
Route::post('menus/sort', 'MenuController@sort');
Route::put('menus/update/{id}', 'MenuController@update');
Route::post('menus/move/{id}/{parentId}', 'MenuController@move');
Route::delete('menus/delete/{id}', 'MenuController@destroy');
Route::get('menus/menutabs/{id}', 'MenuController@menuTabs');
Route::post('menus/ranktabs/{id}', 'MenuController@rankTabs');

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

// 菜单/卡片图标设置
Route::get('icons/index', 'IconController@index');
Route::get('icons/create', 'IconController@create');
Route::post('icons/store', 'IconController@store');
Route::get('icons/show/{id}', 'IconController@show');
Route::get('icons/edit/{id}', 'IconController@edit');
Route::put('icons/update/{id}', 'IconController@update');
Route::delete('icons/delete/{id}', 'IconController@destroy');

Route::get('icon_types/index', 'IconTypeController@index');
Route::get('icon_types/create', 'IconTypeController@create');
Route::post('icon_types/store', 'IconTypeController@store');
Route::get('icon_types/show/{id}', 'IconTypeController@show');
Route::get('icon_types/edit/{id}', 'IconTypeController@edit');
Route::put('icon_types/update/{id}', 'IconTypeController@update');
Route::delete('icon_types/delete/{id}', 'IconTypeController@destroy');

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

// 成绩统计项设置
Route::get('score_ranges/statistics_show', 'ScoreRangeController@statisticsShow');
Route::post('score_ranges/statistics', 'ScoreRangeController@statistics');
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
//Route::get('procedure_logs/index', 'ProcedureLogController@index');
//Route::get('procedure_logs/show/{id}', 'ProcedureLogController@show');
Route::get('procedure_logs/index', 'ProcedureLogController@myProcedure');
Route::get('procedure_logs/pending', 'ProcedureLogController@pending');
Route::get('procedure_logs/show/{first_log_id}    ', 'ProcedureLogController@procedureInfo');
Route::get('procedure_logs/create', 'ProcedureLogController@create');
Route::post('procedure_logs/store', 'ProcedureLogController@store');
Route::post('procedure_logs/decision', 'ProcedureLogController@decision');
Route::post('procedure_logs/upload_medias', 'ProcedureLogController@uploadMedias');
Route::get('procedure_logs/delete_medias/{id}', 'ProcedureLogController@deleteMedias');


//用户管理-用户设置
Route::get('users/index', 'UserController@index');
Route::get('users/create', 'UserController@create');
Route::post('users/store', 'UserController@store');
Route::get('users/show/{id}', 'UserController@show');
Route::get('users/edit/{id}', 'UserController@edit');
Route::put('users/update/{id}', 'UserController@update');
Route::delete('users/delete/{id}', 'UserController@destroy');
Route::post('users/upload_ava/{id}', 'UserController@uploadAvatar');

#用户中心
//个人信息管理
Route::get('personal_infos/edit/{id}', 'PersonalInfoController@edit');
Route::put('personal_infos/update/{id}', 'PersonalInfoController@update');
Route::post('personal_infos/upload_ava/{id}', 'PersonalInfoController@uploadAvatar');

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
Route::get('wap_sites/index', 'WapSiteController@index');
Route::get('wap_sites/create', 'WapSiteController@create');
Route::post('wap_sites/store', 'WapSiteController@store');
Route::get('wap_sites/show/{id}', 'WapSiteController@show');
Route::get('wap_sites/edit/{id}', 'WapSiteController@edit');
Route::put('wap_sites/update/{id}', 'WapSiteController@update');
Route::delete('wap_sites/delete/{id}', 'WapSiteController@destroy');
Route::any('wap_sites/uploadImages', 'WapSiteController@uploadImages');

Route::get('wap_sites/webindex', 'WapSiteController@webindex');


//微网站管理-网站模块管理
Route::get('wap_site_modules/index', 'WapSiteModuleController@index');
Route::get('wap_site_modules/create', 'WapSiteModuleController@create');
Route::post('wap_site_modules/store', 'WapSiteModuleController@store');
Route::get('wap_site_modules/show/{id}', 'WapSiteModuleController@show');
Route::get('wap_site_modules/edit/{id}', 'WapSiteModuleController@edit');
Route::put('wap_site_modules/update/{id}', 'WapSiteModuleController@update');
Route::delete('wap_site_modules/delete/{id}', 'WapSiteModuleController@destroy');
Route::get('wap_site_modules/webindex/{id}', 'WapSiteModuleController@webindex');

//微网站管理-文章管理
Route::get('wsm_articles/index', 'WsmArticleController@index');
Route::get('wsm_articles/create', 'WsmArticleController@create');
Route::post('wsm_articles/store', 'WsmArticleController@store');
Route::get('wsm_articles/show/{id}', 'WsmArticleController@show');
Route::get('wsm_articles/edit/{id}', 'WsmArticleController@edit');
Route::put('wsm_articles/update/{id}', 'WsmArticleController@update');
Route::delete('wsm_articles/delete/{id}', 'WsmArticleController@destroy');
Route::get('wsm_articles/detail/{id}', 'WsmArticleController@detail');


//消息中心-消息管理
Route::get('messages/index', 'MessageController@index');
Route::get('messages/create', 'MessageController@create');
Route::post('messages/store', 'MessageController@store');
Route::get('messages/show/{id}', 'MessageController@show');
Route::get('messages/edit/{id}', 'MessageController@edit');
Route::put('messages/update/{id}', 'MessageController@update');
Route::delete('messages/delete/{id}', 'MessageController@destroy');

//消息中心-消息类型管理
Route::get('message_types/index', 'MessageTypeController@index');
Route::get('message_types/create', 'MessageTypeController@create');
Route::post('message_types/store', 'MessageTypeController@store');
Route::get('message_types/show/{id}', 'MessageTypeController@show');
Route::get('message_types/edit/{id}', 'MessageTypeController@edit');
Route::put('message_types/update/{id}', 'MessageTypeController@update');
Route::delete('message_types/delete/{id}', 'MessageTypeController@destroy');
