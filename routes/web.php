<?php
include_once 'common.php';
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
// Route::get('/fireEvent', function() {
//     event(new eventTrigger());
// });
Route::get('/messages/send', 'MessageController@send');
Route::auth();
# 关闭注册功能
Route::any('register', function() { return redirect('login'); });
Route::get('logout', 'Auth\LoginController@logout');
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
/** 测试用路由 */
Route::get('test/index', 'TestController@index');
Route::get('test/create', 'TestController@create');
Route::get('test', 'TestController@test');
/** 菜单入口路由 */
Route::get('pages/{id}', 'HomeController@menu');
// Route::get('pages/{id}', 'MenuController@page');
/** 用户/通讯录 */
// 教职员工
Route::group(['prefix' => 'educators'], routes('EducatorController'));
Route::group(['prefix' => 'educators'], function () {
    $c = 'EducatorController';
    Route::get('recharge/{id}', $c . '@recharge');
    Route::put('rechargeStore/{id}', $c . '@rechargeStore');
    Route::post('edit/{id}', $c . '@edit');
    Route::post('create', $c . '@create');
    Route::get('export', $c . '@export');
    Route::post('export', $c . '@export');
    Route::post('import', $c . '@import');
});
// 监护人
Route::group(['prefix' => 'custodians'], routes('CustodianController'));
Route::group(['prefix' => 'custodians'], function () {
    $c = 'CustodianController';
    Route::post('edit/{id}', $c . '@edit');
    Route::post('create', $c . '@create');
    Route::get('export', $c . '@export');
    // Route::any('relationship', $c . '@relationship');
});
// 学生
Route::group(['prefix' => 'students'], routes('StudentController'));
Route::group(['prefix' => 'students'], function () {
    $c = 'StudentController';
    Route::post('edit/{id}', $c . '@edit');
    Route::post('create', $c . '@create');
    Route::post('import', $c . '@import');
    Route::get('export', $c . '@export');
    Route::post('export', $c . '@export');
});
// 用户
Route::group(['prefix' => 'users'], routes('UserController'));
Route::group(['prefix' => 'users'], function () {
    $c = 'UserController';
    Route::get('event', $c . '@event');
});
Route::post('users/upload_ava/{id}', 'UserController@uploadAvatar');
/** 成绩管理 */
// 考试管理 - 考试设置.考试类型设置
Route::group(['prefix' => 'exams'], routes('ExamController'));
Route::group(['prefix' => 'exam_types'], routes('ExamTypeController'));
// 成绩管理 - 成绩录入/导入.总成绩录入/导入.成绩统计项设置
Route::group(['prefix' => 'scores'], routes('ScoreController'));
Route::group(['prefix' => 'scores'], function () {
    $c = 'ScoreController';
    Route::get('statistics/{examId}', $c . '@statistics');
    Route::get('export/{examId}', $c . '@export');
    Route::get('clalists/{examId}', $c . '@claLists');
    Route::post('analysis', $c . '@analysis');
    Route::get('analysis', $c . '@analysis');
    Route::post('analydata', $c . '@analydata');
    Route::post('import', $c . '@import');
    Route::get('exports', $c . '@exports');
    Route::post('send', $c . '@send');
    Route::post('send_message', $c . '@send_message');
    Route::get('listdatas/{examId}', $c . '@listdatas');
    Route::get('clastudents/{classId}', $c . '@clastudents');
});
// 总成绩
Route::group(['prefix' => 'score_totals'], function () {
    $c = 'ScoreTotalController';
    Route::get('index', $c . '@index');
    Route::get('stat/{examId}', $c . '@stat');
});
// 消费记录
Route::group(['prefix' => 'consumptions'], function () {
    $c = 'ConsumptionController';
    Route::get('index', $c . '@index');
    Route::get('show', $c . '@show');
    Route::post('stat', $c . '@stat');
    Route::get('export', $c . '@export');
});
Route::group(['prefix' => 'score_ranges'], routes('ScoreRangeController'));
Route::group(['prefix' => 'score_ranges'], function () {
    $c = 'ScoreRangeController';
    Route::get('show_statistics', $c . '@showStatistics');
    Route::post('statistics', $c . '@statistics');
});
// 成绩统计/打印
// 成绩发布
// Route::group(['prefix' => 'scoreSend'], function () {
//     $c = 'Score_SendController';
//     Route::get('/', $c . '@index');
//     Route::get('index', $c . '@index@index');
//     Route::Post('getgrade/{id}', $c . '@getGrade');
//     Route::Post('getclass/{id}', $c . '@getClass');
//     Route::Post('getexam/{id}', $c . '@getExam');
//     Route::Post('getsubject/{id}', $c . '@getSubject');
//     Route::post('preview/{examId}/{classId}/{subjectIds}/{itemId}', $c . '@preview');
// });
/** 考勤管理 */
// 考勤设置 - 考勤时段设置.考勤机设置
Route::group(['prefix' => 'attendance_machines'], routes('AttendanceMachineController'));
Route::group(['prefix' => 'educator_attendance_settings'], routes('EducatorAttendanceSettingController'));
Route::group(['prefix' => 'student_attendance_settings'], routes('StudentAttendanceSettingController'));
// 学生考勤记录
Route::group(['prefix' => 'student_attendances'], function (){
    $c = 'StudentAttendanceController';
    Route::get('index', $c . '@index');
    Route::get('stat', $c . '@stat');
    Route::post('stat', $c . '@stat');
    Route::post('detail', $c . '@detail');
    Route::get('export', $c . '@export');
});
// 教职员工考勤记录
Route::group(['prefix' => 'educator_attendances'], function (){
    $c = 'EducatorAttendanceController';
    Route::get('index', $c . '@index');
    Route::get('stat', $c . '@stat');
    Route::post('stat', $c . '@stat');
    Route::post('detail', $c . '@detail');
    Route::get('export', $c . '@export');
});

/** 课程表管理 */
// 课程表设置
Route::group(['prefix' => 'events'], routes('EventController'));
Route::group(['prefix' => 'events'], function () {
    $c = 'EventController';
    Route::get('calendar_events/{id}', $c . '@calendarEvents');
    Route::post('drag_events', $c . '@dragEvents');
    Route::post('update_time', $c . '@updateTime');
});
/** 自媒体管理 */
// 微网站设置 - 微网站管理.网站模块管理.文章管理
Route::group(['prefix' => 'wap_sites'], routes('WapSiteController'));
Route::any('wap_sites/uploadImages', 'WapSiteController@uploadImages');
Route::get('wap_sites/webindex/{$school_id}', 'WapSiteController@wapHome');
Route::group(['prefix' => 'wap_site_modules'], routes('WapSiteModuleController'));
Route::get('wap_site_modules/webindex/{id}', 'WapSiteModuleController@wapSiteModuleHome');
Route::group(['prefix' => 'wsm_articles'], routes('WsmArticleController'));
Route::get('wsm_articles/detail/{id}', 'WsmArticleController@detail');
/** 投票问卷 */
// 发起
Route::group(['prefix' => 'poll_questionnaires'], routes('PollQuestionnaireController'));
Route::group(['prefix' => 'poll_questionnaire_subjects'], routes('PollQuestionnaireSubjectController'));
Route::group(['prefix' => 'poll_questionnaire_subject_choices'], routes('PollQuestionnaireSubjectChoiceController'));
// 参与
// 查询/统计
Route::group(['prefix' => 'poll_questionnaire_participants'], function () {
    $c = 'PollQuestionnaireParticipantController';
    Route::get('/', $c . '@index');
    Route::get('index', $c . '@index');
    Route::post('show/{id}', $c . '@show');
    Route::put('update', $c . '@update')->name("pqp_update");
});
/** 移动办公 */
// 审批设置 - 流程设置.流程类型设置.流程步骤设置
Route::group(['prefix' => 'procedure_types'], routes('ProcedureTypeController'));
Route::group(['prefix' => 'procedures'], routes('ProcedureController'));
Route::group(['prefix' => 'procedure_steps'], routes('ProcedureStepController'));
Route::get('procedure_steps/getSchoolEducators/{id}', 'ProcedureStepController@getSchoolEducators');
// 审批发起/处理
Route::group(['prefix' => 'procedure_logs'], function () {
    $c = 'ProcedureLogController';
    Route::get('index', $c . '@index');
    Route::get('pending', $c . '@pending');
    Route::get('show/{firstLogId}', $c . '@show');
    Route::get('create', $c . '@create');
    Route::post('store', $c . '@store');
    Route::post('decision', $c . '@decision');
    Route::post('uploadMedias', $c . '@uploadMedias');
    Route::get('deleteMedias/{id}', $c . '@deleteMedias');
});
// 会议助手
Route::group(['prefix' => 'conference_rooms'], routes('ConferenceRoomController'));
Route::group(['prefix' => 'conference_queues'], routes('ConferenceQueueController'));
Route::group(['prefix' => 'conference_participants'], function () {
    $c = 'ConferenceParticipantController';
    Route::get('index', $c . '@index');
    Route::post('store', $c . '@store');
    Route::get('show/{id}', $c . '@show');
});
// 申诉
/** 用户中心 */
Route::get('users/profile','UserController@profile');
Route::get('users/reset','UserController@reset');
Route::post('users/reset','UserController@reset');
Route::get('users/messages','UserController@messages');
Route::get('users/events','UserController@event');
// 个人通讯录
// 消息中心
Route::group(['prefix' => 'messages'], routes('MessageController'));
Route::group(['prefix' => 'messages'], function () {
    $c = 'MessageController';
    Route::post('get_depart_users', $c . '@getDepartmentUsers');
    Route::post('index', $c . '@index');
    Route::get('send', $c . '@send');
    Route::any('uploadFile', $c . '@uploadFile');

});
// 日历
// 个人信息
// Route::group(['prefix' => 'personal_infos'], function () {
//     $c = 'PersonalInfoController';
//     Route::get('index', $c . '@index');
//     Route::put('update/{id}', $c . '@update');
//     Route::post('upload_ava/{id}', $c . '@uploadAvatar');
// });
/** 订单管理 */
Route::group(['prefix' => 'orders'], function () {
    $c = 'OrderController';
    Route::get('index', $c . '@index');
    Route::post('store', $c . '@store');
    Route::get('show/{id}', $c . '@show');
    Route::put('update/{id}', $c . '@update');
    Route::delete('delete/{id}', $c . '@destroy');
});
Route::group(['prefix' => 'combo_types'], routes('ComboTypeController'));
/** 系统设置 */
// 学校设置 - 学校管理.学期设置.教职员工组别设置.学校类型设置
Route::group(['prefix' => 'schools'], routes('SchoolController'));
Route::group(['prefix' => 'semesters'], routes('SemesterController'));
Route::group(['prefix' => 'teams'], routes('TeamController'));
Route::group(['prefix' => 'school_types'], routes('SchoolTypeController'));
// 科目设置 - 科目管理.科目次分类设置
Route::group(['prefix' => 'subjects'], routes('SubjectController'));
Route::get('subjects/query/{id}', 'SubjectController@query');
Route::group(['prefix' => 'subject_modules'], routes('SubjectModuleController'));
Route::group(['prefix' => 'majors'], routes('MajorController'));
// 角色/权限 - 角色管理.权限管理
Route::group(['prefix' => 'groups'], routes('GroupController'));
Route::group(['prefix' => 'groups'], function () {
    Route::post('create', 'GroupController@create');
    Route::post('edit/{id}', 'GroupController@edit');
});
// 年级/班级设置 - 年级管理.班级管理
Route::group(['prefix' => 'grades'], routes('GradeController'));
Route::group(['prefix' => 'classes'], routes('SquadController'));
// 应用设置 - 微信应用管理
Route::group(['prefix' => 'apps'], routes('AppController'));
Route::post('apps/index', 'AppController@index');
Route::get('apps/menu/{id}', 'AppController@menu');
// 图标管理 - 图标设置.图标类型管理
Route::group(['prefix' => 'icons'], routes('IconController'));
Route::group(['prefix' => 'icon_types'], routes('IconTypeController'));
// 消息类型设置 - 消息类型管理
Route::group(['prefix' => 'message_types'], routes('MessageTypeController'));
// 通信方式设置 - 通信方式管理
Route::group(['prefix' => 'comm_types'], routes('CommTypeController'));
// 警告类型设置 - 警告类型管理
Route::group(['prefix' => 'alert_types'], routes('AlertTypeController'));
// 运营者设置 - 企业设置
Route::group(['prefix' => 'department_types'], routes('DepartmentTypeController'));
Route::group(['prefix' => 'departments'], routeItem('DepartmentController'));
Route::group(['prefix' => 'departments'], function () {
    $c = 'DepartmentController';
    Route::post('index', $c . '@index');
    Route::post('move/{id}/{parentId?}', $c . '@move');
    Route::post('sort', $c . '@sort');
});
Route::group(['prefix' => 'companies'], routes('CompanyController'));
Route::group(['prefix' => 'corps'], routes('CorpController'));
// 菜单管理 - action设置.卡片设置.菜单设置
Route::group(['prefix' => 'actions'], routes('ActionController'));
Route::group(['prefix' => 'tabs'], routes('TabController'));
Route::group(['prefix' => 'menus'], routeItem('MenuController'));
Route::group(['prefix' => 'menus'], function () {
    $c = 'MenuController';
    Route::post('index', $c . '@index');
    Route::post('sort', $c . '@sort');
    Route::post('move/{id}/{parentId?}', $c . '@move');
    Route::get('menutabs/{id}', $c . '@menuTabs');
    Route::post('ranktabs/{id}', $c . '@rankTabs');
});
// 管理员
Route::group(['prefix' => 'operators'], routes('OperatorController'));
Route::group(['prefix' => 'operators'], function() {
    $c = 'OperatorController';
    Route::post('create', $c . '@create');
    Route::post('edit/{id}', $c . '@edit');
});

# --------------------------------------------------------------------------------
// 消息中心
$c = 'Wechat\MessageCenterController';
Route::get('message_center', $c . '@index');
Route::post('message_center', $c . '@index');
Route::get('message_create', $c . '@create');
Route::post('message_create', $c . '@create');
Route::post('message_store', $c . '@store');
Route::get('message_show/{id}', $c . '@show');
Route::get('message_update/{id}', $c . '@updateStatus');
Route::delete('message_delete/{id}', $c . '@destroy');
Route::post('message_upload', $c . '@upload');
Route::get('message_dept/{id}', $c . '@getNextDept');
Route::post('message_replay', $c . '@replay');
Route::post('message_replaylist', $c . '@replayList');
Route::delete('message_replaydel/{id}', $c . '@replayDestroy');
//布置作业
Route::get('homework', 'Wechat\HomeWorkController@index');
//微网站
$c = 'Wechat\MobileSiteController';
Route::any('wapsite/home', $c . '@wapHome');
Route::any('wapsite/module/home', $c . '@wapSiteModuleHome');
Route::any('wapsite/article/home', $c . '@articleHome');
// 考勤
$c = 'Wechat\AttendanceController';
Route::get('lists', $c . '@index');
Route::get('attendance_records/{id}', $c . '@records');
Route::post('attendance_records/{id?}', $c . '@records');
Route::post('attendance_charts', $c . '@stuChart');
Route::get('attendance_rules/{id}', $c . '@getRules');
Route::get('attendance_date', $c . '@dateRules');
// 成绩中心
$c = 'Wechat\ScoreCenterController';
Route::any('wechat/score/score_lists', $c . '@index');
Route::get('wechat/score/detail', $c . '@detail');
Route::get('wechat/score/student_detail', $c . '@subjectDetail');
Route::post('wechat/score/student_detail', $c . '@subjectDetail');
Route::any('wechat/score/show', $c . '@show');
Route::get('wechat/score/analysis', $c . '@analysis');
Route::get('wechat/score/cus_total', $c . '@cusTotal');


/** Broadcasting test */
Route::get('event', function () {
    event(new \App\Events\ContactImportTrigger([
        'user' => Auth::user(),
        'type' => 'educator'
    ]));
});

Route::get('listen', 'TestController@listen');
