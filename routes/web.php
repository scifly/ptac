<?php
include_once 'common.php';

use App\Models\Corp;
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
# Route::get('/fireEvent', function() {
#     event(new eventTrigger());
# });
Route::get('/messages/send', 'MessageController@send');
Route::auth();

# 关闭注册功能
Route::any('register', function () { return redirect('login'); });
Route::get('logout', 'Auth\LoginController@logout');
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
/** 测试用路由 */
Route::any('test/index', 'TestController@index');
Route::get('test/create', 'TestController@create');
Route::get('test', 'TestController@test');
/** Broadcasting test */
Route::get('event', function () {
    event(new \App\Events\JobResponse([
        'userId' => 1,
        'title' => '广播测试',
        'statusCode' => \App\Helpers\HttpStatusCode::OK,
        'message' => '工作正常'
    ]));
});
Route::get('listen', 'TestController@listen');

/** 菜单入口路由 */
Route::get('pages/{id}', 'HomeController@menu');
# Route::get('pages/{id}', 'MenuController@page');

/** 用户/通讯录 */
# 教职员工
Route::group(['prefix' => 'educators'], routes('EducatorController'));
Route::group(['prefix' => 'educators'], function () {
    $c = 'EducatorController';
    Route::get('recharge/{id}', $c . '@recharge');
    Route::put('recharge/{id}', $c . '@recharge');
    Route::post('edit/{id}', $c . '@edit');
    Route::post('create', $c . '@create');
    Route::get('export', $c . '@export');
    Route::post('export', $c . '@export');
    Route::post('import', $c . '@import');
});
# 监护人
Route::group(['prefix' => 'custodians'], routes('CustodianController'));
Route::group(['prefix' => 'custodians'], function () {
    $c = 'CustodianController';
    Route::post('edit/{id}', $c . '@edit');
    Route::post('create', $c . '@create');
    Route::get('export', $c . '@export');
    Route::post('export', $c . '@export');
    # Route::any('relationship', $c . '@relationship');
});
# 学生
Route::group(['prefix' => 'students'], routes('StudentController'));
Route::group(['prefix' => 'students'], function () {
    $c = 'StudentController';
    Route::post('edit/{id}', $c . '@edit');
    Route::post('create', $c . '@create');
    Route::post('import', $c . '@import');
    Route::get('export', $c . '@export');
    Route::post('export', $c . '@export');
});
# 标签
Route::group(['prefix' => 'tags'], routes('TagController'));
Route::post('tags/create', 'TagController@create');
Route::post('tags/edit', 'TagController@edit');
# 用户中心
Route::group(['prefix' => 'users'], function () {
    $c = 'UserController';
    Route::get('edit', $c . '@edit');
    Route::put('update', $c . '@update');
    Route::get('reset', $c. '@reset');
    Route::post('reset', $c . '@reset');
    Route::get('message', $c . '@message');
    Route::get('event', $c . '@event');
});

/** 成绩管理 */
# 考试管理 - 考试设置.考试类型设置
Route::group(['prefix' => 'exams'], routes('ExamController'));
Route::group(['prefix' => 'exam_types'], routes('ExamTypeController'));

# 成绩管理 - 成绩录入/导入.总成绩录入/导入.成绩统计项设置
Route::group(['prefix' => 'scores'], function () {
    $c = 'ScoreController';
    Route::get('index', $c . '@index');
    Route::get('create/{examId?}', $c . '@create');
    Route::get('edit/{id}/{examId?}', $c . '@edit');
    Route::post('store', $c . '@store');
    Route::put('update/{id?}', $c . '@update');
    Route::delete('delete/{id?}', $c . '@destroy');
    Route::get('rank/{examId}', $c . '@rank');
    Route::get('import/{examId?}', $c . '@import');
    Route::post('import/{examId?}', $c . '@import');
    Route::get('export/{examId?}', $c . '@export');
    Route::post('export/{examId?}', $c . '@export');
    Route::get('stat', $c . '@stat');
    Route::post('stat', $c . '@stat');
    Route::post('send', $c . '@send');
    Route::get('clastudents/{classId}', $c . '@clastudents');
});
# 总成绩
Route::group(['prefix' => 'score_totals'], function () {
    $c = 'ScoreTotalController';
    Route::get('index', $c . '@index');
    Route::get('stat/{examId}', $c . '@stat');
});
# 消费记录
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
    Route::get('stat', $c . '@stat');
    Route::post('stat', $c . '@stat');
});

/** 考勤管理 */
# 考勤设置 - 考勤时段设置.考勤机设置
Route::group(['prefix' => 'attendance_machines'], routes('AttendanceMachineController'));
Route::group(['prefix' => 'educator_attendance_settings'], routes('EducatorAttendanceSettingController'));
Route::group(['prefix' => 'student_attendance_settings'], routes('StudentAttendanceSettingController'));
# 学生考勤记录
Route::group(['prefix' => 'student_attendances'], function () {
    $c = 'StudentAttendanceController';
    Route::get('index', $c . '@index');
    Route::get('stat', $c . '@stat');
    Route::post('stat', $c . '@stat');
    Route::post('detail', $c . '@detail');
    Route::get('export', $c . '@export');
});
# 教职员工考勤记录
Route::group(['prefix' => 'educator_attendances'], function () {
    $c = 'EducatorAttendanceController';
    Route::get('index', $c . '@index');
    Route::get('stat', $c . '@stat');
    Route::post('stat', $c . '@stat');
    Route::post('detail', $c . '@detail');
    Route::get('export', $c . '@export');
});

/** 课程表管理 */
# 课程表设置
Route::group(['prefix' => 'events'], routes('EventController'));
Route::group(['prefix' => 'events'], function () {
    $c = 'EventController';
    Route::get('calendar_events/{id}', $c . '@calendarEvents');
    Route::post('drag_events', $c . '@dragEvents');
    Route::post('update_time', $c . '@updateTime');
});

/** 自媒体管理 */
# 微网站设置 - 微网站管理.网站模块管理.文章管理
Route::group(['prefix' => 'wap_sites'], function () {
    $c = 'WapSiteController';
    Route::get('index', $c . '@index');
    Route::get('edit/{id}', $c . '@edit');
    Route::post('edit/{id}', $c . '@edit');
    Route::put('update/{id}', $c . '@update');
});
Route::group(['prefix' => 'wap_site_modules'], function () {
    $c = 'WapSiteModuleController';
    Route::get('index', $c . '@index');
    Route::get('create', $c . '@create');
    Route::post('create', $c . '@create');
    Route::post('store', $c . '@store');
    Route::get('edit/{id}', $c . '@edit');
    Route::post('edit/{id}', $c . '@edit');
    Route::put('update/{id}', $c . '@update');
    Route::delete('delete/{id}', $c . '@destroy');
});
Route::group(['prefix' => 'wsm_articles'], function () {
    $c = 'WsmArticleController';
    Route::get('index', $c . '@index');
    Route::get('create', $c . '@create');
    Route::post('create', $c . '@create');
    Route::post('store', $c . '@store');
    Route::get('edit/{id}', $c . '@edit');
    Route::post('edit/{id}', $c . '@edit');
    Route::put('update/{id}', $c . '@update');
    Route::delete('delete/{id}', $c . '@destroy');
});

/** 投票问卷 */
# 发起
Route::group(['prefix' => 'poll_questionnaires'], routes('PollQuestionnaireController'));
Route::group(['prefix' => 'poll_questionnaire_subjects'], routes('PollQuestionnaireSubjectController'));
Route::group(['prefix' => 'poll_questionnaire_subject_choices'], routes('PollQuestionnaireSubjectChoiceController'));
# 参与
# 查询/统计
Route::group(['prefix' => 'poll_questionnaire_participants'], function () {
    $c = 'PollQuestionnaireParticipantController';
    Route::get('/', $c . '@index');
    Route::get('index', $c . '@index');
    Route::post('show/{id}', $c . '@show');
    Route::put('update', $c . '@update')->name("pqp_update");
});

/** 移动办公 */
# 审批设置 - 流程设置.流程类型设置.流程步骤设置
Route::group(['prefix' => 'procedure_types'], routes('ProcedureTypeController'));
Route::group(['prefix' => 'procedures'], routes('ProcedureController'));
Route::group(['prefix' => 'procedure_steps'], routes('ProcedureStepController'));
Route::get('procedure_steps/getSchoolEducators/{id}', 'ProcedureStepController@getSchoolEducators');
# 审批发起/处理
Route::group(['prefix' => 'procedure_logs'], function () {
    $c = 'ProcedureLogController';
    Route::get('index', $c . '@index');
    Route::get('pending', $c . '@pending');
    Route::get('show/{id}', $c . '@show');
    Route::get('create', $c . '@create');
    Route::post('store', $c . '@store');
    Route::post('sanction', $c . '@sanction');
    Route::post('upload', $c . '@upload');
    Route::get('delete/{id}', $c . '@delete');
});

# 会议助手
Route::group(['prefix' => 'conference_rooms'], routes('ConferenceRoomController'));
Route::group(['prefix' => 'conference_queues'], routes('ConferenceQueueController'));
Route::group(['prefix' => 'conference_participants'], function () {
    $c = 'ConferenceParticipantController';
    Route::get('index', $c . '@index');
    Route::post('store', $c . '@store');
    Route::get('show/{id}', $c . '@show');
});

# 消息中心
Route::group(['prefix' => 'messages'], function () {
    $c = 'MessageController';
    Route::get('index', $c . '@index');
    Route::post('index', $c . '@index');
    Route::post('store', $c . '@store');
    Route::get('edit/{id}', $c . '@edit');
    Route::put('update/{id?}', $c . '@update');
    Route::get('show/{id}', $c . '@show');
    Route::post('send', $c . '@send');
    Route::delete('delete/{id?}', $c . '@destroy');
});
# 日历
# 个人信息
# Route::group(['prefix' => 'personal_infos'], function () {
#     $c = 'PersonalInfoController';
#     Route::get('index', $c . '@index');
#     Route::put('update/{id}', $c . '@update');
#     Route::post('upload_ava/{id}', $c . '@uploadAvatar');
# });

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
# 学校设置 - 学校管理.学期设置
Route::group(['prefix' => 'schools'], routes('SchoolController'));
Route::group(['prefix' => 'semesters'], routes('SemesterController'));
# 科目设置 - 科目管理.科目次分类设置
Route::group(['prefix' => 'subjects'], routes('SubjectController'));
Route::group(['prefix' => 'subject_modules'], routes('SubjectModuleController'));
Route::group(['prefix' => 'majors'], routes('MajorController'));
# 角色/权限 - 角色管理.权限管理
Route::group(['prefix' => 'groups'], routes('GroupController'));
Route::group(['prefix' => 'groups'], function () {
    Route::post('create', 'GroupController@create');
    Route::post('edit/{id}', 'GroupController@edit');
});
# 年级/班级设置 - 年级管理.班级管理
Route::group(['prefix' => 'grades'], routes('GradeController'));
Route::group(['prefix' => 'classes'], routes('SquadController'));
# 应用设置 - 微信应用管理
Route::group(['prefix' => 'apps'], function () {
    Route::get('index', 'AppController@index');
    Route::post('index', 'AppController@index');
    Route::get('edit/{id}', 'AppController@edit');
    Route::put('update/{id}', 'AppController@update');
    Route::delete('delete/{id}', 'AppController@destroy');
});
# 图标管理 - 图标设置.图标类型管理
Route::group(['prefix' => 'icons'], routes('IconController'));
Route::group(['prefix' => 'icon_types'], routes('IconTypeController'));
# 运营者设置 - 企业设置

# 部门管理
Route::group(['prefix' => 'departments'], function () {
    $c = 'DepartmentController';
    Route::get('index', $c . '@index');
    Route::post('index', $c . '@index');
    Route::get('create/{parentId}', $c . '@create');
    Route::post('store', $c . '@store');
    Route::get('edit/{id}', $c . '@edit');
    Route::put('update/{id}', $c . '@update');
    Route::delete('delete/{id}', $c . '@destroy');
});
Route::group(['prefix' => 'companies'], routes('CompanyController'));
Route::group(['prefix' => 'corps'], routes('CorpController'));
# 菜单管理 - action设置.卡片设置.菜单设置
Route::group(['prefix' => 'actions'], routes('ActionController'));
Route::group(['prefix' => 'tabs'], function () {
    $c = 'TabController';
    Route::get('index', $c . '@index');
    Route::get('edit/{id}', $c . '@edit');
    Route::put('update/{id?}', $c . '@update');
});
# 菜单管理
Route::group(['prefix' => 'menus'], function () {
    $c = 'MenuController';
    Route::get('index', $c . '@index');
    Route::post('index', $c . '@index');
    Route::get('create/{parentId}', $c . '@create');
    Route::get('edit/{id}', $c . '@edit');
    Route::post('store', $c . '@store');
    Route::put('update/{id}', $c . '@update');
    Route::delete('delete/{id}', $c . '@destroy');
    Route::get('sort/{id}', $c . '@sort');
    Route::post('sort/{id}', $c . '@sort');
});
# 管理员
Route::group(['prefix' => 'operators'], routes('OperatorController'));
Route::group(['prefix' => 'operators'], function () {
    $c = 'OperatorController';
    Route::post('create', $c . '@create');
    Route::post('edit/{id}', $c . '@edit');
});
# 合作伙伴
Route::group(['prefix' => 'partners'], routes('PartnerController'));
# (运营)系统设置
Route::group(['prefix' => 'action_types'], routes('ActionTypeController'));
Route::group(['prefix' => 'message_types'], routes('MessageTypeController'));
Route::group(['prefix' => 'comm_types'], routes('CommTypeController'));
Route::group(['prefix' => 'alert_types'], routes('AlertTypeController'));
Route::group(['prefix' => 'department_types'], routes('DepartmentTypeController'));
Route::group(['prefix' => 'menu_types'], routes('MenuTypeController'));
Route::group(['prefix' => 'media_types'], routes('MediaTypeController'));
Route::group(['prefix' => 'attachment_types'], routes('AttachmentTypeController'));
Route::group(['prefix' => 'school_types'], routes('SchoolTypeController'));

/** 微信端路由 -------------------------------------------------------------------------------------------------------- */
Route::get('sms/{urlcode}', 'Wechat\WechatSmsController@show');    # 未关注的用户查看微信消息详情的链接
$acronyms = Corp::pluck('acronym')->toArray();
foreach ($acronyms as $acronym) {
    app_routes($acronym);
}

/** 演示 ------------------------------------------------------------------------------------------------------------- */
$ctlr = 'DemoController@';
$prefix = 'demos/';
Route::get($prefix . 'index', $ctlr . 'index');
Route::get($prefix . 'safe', $ctlr . 'safe');
Route::get($prefix . 'wisdom', $ctlr . 'wisdom');
Route::get($prefix . 'classroom', $ctlr . 'classroom');
Route::get($prefix . 'info', $ctlr . 'info');