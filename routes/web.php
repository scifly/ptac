<?php

use App\Events\eventTrigger;
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
Route::get('/fireEvent', function() {
    event(new eventTrigger());
});

Route::auth();
# 关闭注册功能
Route::any('register', function() { return redirect('login'); });
Route::get('logout', 'Auth\LoginController@logout');
// Route::get('/', function() { return 'Dashboard'; });
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
/** 测试用路由 */
Route::get('test/index', 'TestController@index');
Route::get('test/create', 'TestController@create');
Route::get('test', 'TestController@test');
/** 菜单入口路由 */
Route::get('pages/{id}', 'HomeController@menu');
/** 用户/通讯录 */
// 教职员工
Route::group(['prefix' => 'educators'], routes('EducatorController'));
Route::group(['prefix' => 'educators'], function () {
    $ctlr = 'EducatorController';
    Route::get('recharge/{id}', $ctlr . '@recharge');
    Route::put('rechargeStore/{id}', $ctlr . '@rechargeStore');
    Route::post('edit/{id}', $ctlr . '@edit');
    Route::post('create', $ctlr . '@create');
    Route::get('export', $ctlr . '@export');
    
});
// 监护人
Route::group(['prefix' => 'custodians'], routes('CustodianController'));
Route::group(['prefix' => 'custodians'], function () {
    $ctlr = 'CustodianController';
    Route::post('edit/{id}', $ctlr . '@edit');
    Route::post('create', $ctlr . '@create');
    // Route::any('relationship', $ctlr . '@relationship');
});
// 学生
Route::group(['prefix' => 'students'], routes('StudentController'));
Route::group(['prefix' => 'students'], function () {
    $ctlr = 'StudentController';
    Route::post('edit/{id}', $ctlr . '@edit');
    Route::post('create', $ctlr . '@create');
    Route::post('import', $ctlr . '@import');
    Route::get('export', $ctlr . '@export');
});
// 用户
Route::group(['prefix' => 'users'], routes('UserController'));
Route::post('users/upload_ava/{id}', 'UserController@uploadAvatar');
/** 成绩管理 */
// 考试管理 - 考试设置.考试类型设置
Route::group(['prefix' => 'exams'], routes('ExamController'));
Route::group(['prefix' => 'exam_types'], routes('ExamTypeController'));
// 成绩管理 - 成绩录入/导入.总成绩录入/导入.成绩统计项设置
Route::group(['prefix' => 'scores'], routes('ScoreController'));
Route::group(['prefix' => 'scores'], function () {
    $ctlr = 'ScoreController';
    Route::get('statistics/{examId}', $ctlr . '@statistics');
    Route::get('export/{examId}', $ctlr . '@export');
    Route::get('import', $ctlr . '@import');
});
Route::group(['prefix' => 'score_totals'], function () {
    $ctlr = 'ScoreTotalController';
    Route::get('index', $ctlr . '@index');
    Route::get('show/{id}', $ctlr . '@show');
    Route::get('statistics/{examId}', $ctlr . '@statistics');
});
Route::group(['prefix' => 'score_ranges'], routes('ScoreRangeController'));
Route::group(['prefix' => 'score_ranges'], function () {
    $ctlr = 'ScoreRangeController';
    Route::get('show_statistics', $ctlr . '@showStatistics');
    Route::post('statistics', $ctlr . '@statistics');
});
// 成绩统计/打印
// 成绩发布
Route::group(['prefix' => 'scoreSend'], function () {
    $ctlr = 'Score_SendController';
    Route::get('/', $ctlr . '@index');
    Route::get('index', $ctlr . '@index@index');
    Route::Post('getgrade/{id}', $ctlr . '@getGrade');
    Route::Post('getclass/{id}', $ctlr . '@getClass');
    Route::Post('getexam/{id}', $ctlr . '@getExam');
    Route::Post('getsubject/{id}', $ctlr . '@getSubject');
    Route::post('preview/{examId}/{classId}/{subjectIds}/{itemId}', $ctlr . '@preview');
});
/** 考勤管理 */
// 考勤设置 - 考勤时段设置.考勤机设置
Route::group(['prefix' => 'attendance_machines'], routes('AttendanceMachineController'));
Route::group(['prefix' => 'educator_attendance_settings'], routes('EducatorAttendanceSettingController'));
Route::group(['prefix' => 'student_attendance_settings'], routes('StudentAttendanceSettingController'));
// 考勤查询/统计
/** 课程表管理 */
// 课程表设置
Route::group(['prefix' => 'events'], routes('EventController'));
Route::group(['prefix' => 'events'], function () {
    $ctlr = 'EventController';
    Route::get('calendar_events/{id}', $ctlr . '@calendarEvents');
    Route::post('drag_events', $ctlr . '@dragEvents');
    Route::post('update_time', $ctlr . '@updateTime');
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
Route::group(['prefix' => 'pq_subjects'], routes('PqSubjectController'));
Route::group(['prefix' => 'pq_choices'], routes('PqChoiceController'));
// 参与
// 查询/统计
Route::group(['prefix' => 'pollQuestionnaireParticpation'], function () {
    $ctlr = 'PqParticipantController';
    Route::get('/', $ctlr . '@index');
    Route::get('index', $ctlr . '@index');
    Route::post('show/{id}', $ctlr . '@show');
    Route::put('update', $ctlr . '@update')->name("pqp_update");
});
/** 移动办公 */
// 审批设置 - 流程设置.流程类型设置.流程步骤设置
Route::group(['prefix' => 'procedure_types'], routes('ProcedureTypeController'));
Route::group(['prefix' => 'procedures'], routes('ProcedureController'));
Route::group(['prefix' => 'procedure_steps'], routes('ProcedureStepController'));
Route::get('procedure_steps/getSchoolEducators/{id}', 'ProcedureStepController@getSchoolEducators');
// 审批发起/处理
Route::group(['prefix' => 'procedure_logs'], function () {
    $ctlr = 'ProcedureLogController';
    Route::get('index', $ctlr . '@index');
    Route::get('pending', $ctlr . '@pending');
    Route::get('show/{firstLogId}    ', $ctlr . '@show');
    Route::get('create', $ctlr . '@create');
    Route::post('store', $ctlr . '@store');
    Route::post('decision', $ctlr . '@decision');
    Route::post('upload_medias', $ctlr . '@uploadMedias');
    Route::get('delete_medias/{id}', $ctlr . '@deleteMedias');
});
// 会议助手
Route::group(['prefix' => 'conference_rooms'], routes('ConferenceRoomController'));
Route::group(['prefix' => 'conference_queues'], routes('ConferenceQueueController'));
Route::group(['prefix' => 'conference_participants'], function () {
    $ctlr = 'ConferenceParticipantController';
    Route::get('index', $ctlr . '@index');
    Route::post('store', $ctlr . '@store');
    Route::get('show/{id}', $ctlr . '@show');
});
// 申诉
/** 用户中心 */
// 个人通讯录
// 消息中心
Route::group(['prefix' => 'messages'], routes('MessageController'));
Route::group(['prefix' => 'messages'], function () {
    $ctlr = 'MessageController';
    Route::post('get_depart_users', $ctlr . '@getDepartmentUsers');
});
// 日历
// 个人信息
Route::group(['prefix' => 'personal_infos'], function () {
    $ctlr = 'PersonalInfoController';
    Route::get('index', $ctlr . '@index');
    Route::put('update/{id}', $ctlr . '@update');
    Route::post('upload_ava/{id}', $ctlr . '@uploadAvatar');
});
/** 订单管理 */
Route::group(['prefix' => 'orders'], function () {
    $ctlr = 'OrderController';
    Route::get('index', $ctlr . '@index');
    Route::post('store', $ctlr . '@store');
    Route::get('show/{id}', $ctlr . '@show');
    Route::put('update/{id}', $ctlr . '@update');
    Route::delete('delete/{id}', $ctlr . '@destroy');
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
Route::group(['prefix' => 'departments'], routes('DepartmentController'));
Route::group(['prefix' => 'departments'], function () {
    $ctlr = 'DepartmentController';
    Route::post('index', $ctlr . '@index');
    Route::post('move/{id}/{parentId?}', $ctlr . '@move');
    Route::post('sort', $ctlr . '@sort');
});
Route::group(['prefix' => 'companies'], routes('CompanyController'));
Route::group(['prefix' => 'corps'], routes('CorpController'));
// 菜单管理 - action设置.卡片设置.菜单设置
Route::group(['prefix' => 'actions'], routes('ActionController'));
Route::group(['prefix' => 'tabs'], routes('TabController'));
Route::group(['prefix' => 'menus'], routes('MenuController'));
Route::group(['prefix' => 'menus'], function () {
    $ctlr = 'MenuController';
    Route::post('index', $ctlr . '@index');
    Route::post('sort', $ctlr . '@sort');
    Route::post('move/{id}/{parentId?}', $ctlr . '@move');
    Route::get('menutabs/{id}', $ctlr . '@menuTabs');
    Route::post('ranktabs/{id}', $ctlr . '@rankTabs');
});
// 管理员
Route::group(['prefix' => 'operators'], routes('OperatorController'));
Route::group(['prefix' => 'operators'], function() {
    $ctlr = 'OperatorController';
    Route::post('create', $ctlr . '@create');
    Route::post('edit/{id}', $ctlr . '@edit');
});
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