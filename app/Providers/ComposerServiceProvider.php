<?php
namespace App\Providers;

use App\Helpers\ModelTrait;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Class ComposerServiceProvider
 * @package App\Providers
 */
class ComposerServiceProvider extends ServiceProvider {
    
    use ModelTrait;
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
    
        $ns = 'App\Http\ViewComposers\\';
        
        # 功能 - Action
        View::composer('action.index', $ns . 'ActionIndexComposer');
        View::composer('action.create_edit', $ns . 'ActionComposer');
    
        # 功能类型 - ActionType
        View::composer('action_type.index', $ns . 'ActionTypeIndexComposer');
        View::composer('action_type.create_edit', $ns . 'ActionTypeComposer');
    
        # 警告类型 - AlertType
        View::composer('alert_type.index', $ns . 'AlertTypeIndexComposer');
        View::composer('alert_type.create_edit', $ns . 'AlertTypeComposer');
    
        # 企业应用 - App
        View::composer('app.index', $ns . 'AppIndexComposer');
        View::composer('app.edit', $ns . 'AppComposer');
    
        # 附件类型 - AttachmentType
        View::composer('attachment_type.index', $ns . 'AttachmentTypeIndexComposer');
        View::composer('attachment_type.create_edit', $ns . 'AttachmentTypeComposer');

        # 考勤机 - AttendanceMachine
        View::composer('attendance_machine.index', $ns . 'AttendanceMachineIndexComposer');
        View::composer('attendance_machine.create_edit', $ns . 'AttendanceMachineComposer');
    
        # 通信类型 - CommType
        View::composer('comm_type.index', $ns . 'CommTypeIndexComposer');
        View::composer('comm_type.create_edit', $ns . 'CommTypeComposer');
    
        # 套餐类型 - ComboType
        View::composer('combo_type.index', $ns . 'ComboTypeIndexComposer');
        View::composer('combo_type.create_edit', $ns . 'ComboTypeComposer');
    
        # 运营者 - Company
        View::composer('company.index', $ns . 'CompanyIndexComposer');
        View::composer('company.create_edit', $ns . 'CompanyComposer');
    
        # 与会者 - ConferenceParticipant
        View::composer('conference_participant.index', $ns . 'ConferenceParticipantIndexComposer');
    
        # 会议 - ConferenceQueue
        View::composer('conference_queue.index', $ns . 'ConferenceQueueIndexComposer');
        View::composer('conference_queue.create_edit', $ns . 'ConferenceQueueComposer');
        View::composer('conference_queue.edit', $ns . 'ConferenceQueueEditComposer');
    
        # 会议室 - ConferenceRoom
        View::composer('conference_room.index', $ns . 'ConferenceRoomIndexComposer');
        View::composer('conference_room.create_edit', $ns . 'ConferenceRoomComposer');
    
        # 学生消费 - Consumption
        View::composer('consumption.index', $ns . 'ConsumptionIndexComposer');
        View::composer('consumption.show', $ns . 'ConsumptionStatComposer');
    
        # 微信企业 - Corp
        View::composer('corp.index', $ns . 'CorpIndexComposer');
        View::composer('corp.create_edit', $ns . 'CorpComposer');
    
        # 通讯录.监护人 - Custodian
        View::composer('custodian.index', $ns . 'CustodianIndexComposer');
        View::composer('custodian.create_edit', $ns . 'CustodianComposer');
        View::composer('custodian.edit', $ns . 'CustodianComposer');
        View::composer('custodian.create', $ns . 'CustodianComposer');
        View::composer('custodian.relationship', $ns . 'CustodianRelationshipComposer');
    
        # 部门 - Department
        View::composer('department.index', $ns . 'DepartmentIndexComposer');
        View::composer('department.create_edit', $ns . 'DepartmentComposer');
    
        # 部门类型 - DepartmentType
        View::composer('department_type.index', $ns . 'DepartmentTypeIndexComposer');
        View::composer('department_type.create_edit', $ns . 'DepartmentTypeComposer');
    
        # 教职员工考勤 - EducatorAttendance
        View::composer('educator_attendance.index', $ns . 'EducatorAttendanceIndexComposer');
        View::composer('educator_attendance.stat', $ns . 'EducatorAttendanceStatComposer');
    
        # 教职员工考勤设置 - EducatorAttendanceSetting
        View::composer('educator_attendance_setting.index', $ns . 'EducatorAttendanceSettingIndexComposer');
        View::composer('educator_attendance_setting.create_edit', $ns . 'EducatorAttendanceSettingComposer');
    
        # 通讯录.教职员工 - Educator
        View::composer('educator.index', $ns . 'EducatorIndexComposer');
        View::composer('educator.create_edit', $ns . 'EducatorComposer');
        View::composer('educator.recharge', $ns . 'EducatorRechargeComposer');
    
        # 日历 - Event
        View::composer('event.index', $ns . 'EventIndexComposer');
        View::composer('event.show', $ns . 'EventComposer');
    
        # 考试 - Exam
        View::composer('exam.index', $ns . 'ExamIndexComposer');
        View::composer('exam.create_edit', $ns . 'ExamComposer');
        View::composer('exam.show', $ns . 'ExamShowComposer');
    
        # 考试类型 - ExamType
        View::composer('exam_type.index', $ns . 'ExamTypeIndexComposer');
        View::composer('exam_type.create_edit', $ns . 'ExamTypeComposer');
    
        # 年级 - Grade
        View::composer('grade.index', $ns . 'GradeIndexComposer');
        View::composer('grade.create_edit', $ns . 'GradeComposer');
    
        # 角色/权限 - Group
        View::composer('group.index', $ns . 'GroupIndexComposer');
        View::composer('group.create_edit', $ns . 'GroupComposer');
        View::composer('group.create', $ns . 'GroupCreateComposer');
        View::composer('group.edit', $ns . 'GroupEditComposer');
    
        # 图标 - Icon
        View::composer('icon.index', $ns . 'IconIndexComposer');
        View::composer('icon.create_edit', $ns . 'IconComposer');
    
        # 专业 - Major
        View::composer('major.index', $ns . 'MajorIndexComposer');
        View::composer('major.create_edit', $ns . 'MajorComposer');
    
        # 媒体类型 - MediaType
        View::composer('media_type.create_edit', $ns . 'MediaTypeComposer');
        View::composer('media_type.index', $ns . 'MediaTypeIndexComposer');
    
        # 菜单 - Menu
        View::composer('menu.index', $ns . 'MenuIndexComposer');
        View::composer('menu.create_edit', $ns . 'MenuComposer');
        View::composer('menu.menu_tabs', $ns . 'MenuSortComposer');
    
        # 菜单类型 - MenuType
        View::composer('menu_type.index', $ns . 'MenuTypeIndexComposer');
        View::composer('menu_type.create_edit', $ns . 'MenuTypeComposer');
    
        # 消息中心 - MessageCenter
        View::composer('wechat.message_center.index', $ns . 'MessageCenterIndexComposer');
        View::composer('wechat.message_center.create_edit', $ns . 'MessageCenterComposer');
        View::composer('wechat.message_center.show', $ns . 'MessageCenterShowComposer');
    
        # 消息 - Message
        // View::composer('message.create_edit', $ns . 'MessageComposer');
        View::composer('message.index', $ns . 'MessageIndexComposer');
    
        # 消息类型 - MessageType
        View::composer('message_type.index', $ns . 'MessageTypeIndexComposer');
        View::composer('message_type.create_edit', $ns . 'MessageTypeIndexComposer');
    
        # 超级用户 - Operator
        View::composer('operator.index', $ns . 'OperatorIndexComposer');
        View::composer('operator.create_edit', $ns . 'OperatorComposer');
    
        # 投票问卷 - PollQuestionnaire
        View::composer('poll_questionnaire.index', $ns . 'PollQuestionnaireIndexComposer');
        View::composer('poll_questionnaire.create_edit', $ns . 'PollQuestionnaireComposer');
    
        # 投票问卷问题选项 - PollQuestionnaireSubjectChoice
        View::composer('pq_choice.create_edit', $ns . 'PqChoiceComposer');
        View::composer('pq_choice.index', $ns . 'PqChoiceIndexComposer');
    
        # 投票问卷题目 - PollQuestionnaireSubject
        View::composer('pq_subject.create_edit', $ns . 'PqSubjectComposer');
        View::composer('pq_subject.index', $ns . 'PqSubjectIndexComposer');
    
        # 审批流程 - Procedure
        View::composer('procedure.index', $ns . 'ProcedureIndexComposer');
        View::composer('procedure.create_edit', $ns . 'ProcedureComposer');
    
        # 审批流程步骤 - ProcedureStep
        View::composer('procedure_step.index', $ns . 'ProcedureStepIndexComposer');
        View::composer('procedure_step.create_edit', $ns . 'ProcedureStepComposer');
    
        # 审批流程类型 - ProcedureType
        View::composer('procedure_type.index', $ns . 'ProcedureTypeIndexComposer');
        View::composer('procedure_type.create_edit', $ns . 'ProcedureTypeComposer');
    
        # 学校 - School
        View::composer('school.index', $ns . 'SchoolIndexComposer');
        View::composer('school.create_edit', $ns . 'SchoolComposer');
        View::composer('school.show', $ns . 'SchoolShowComposer');
    
        # 学校类型 - SchoolType
        View::composer('school_type.index', $ns . 'SchoolTypeIndexComposer');
        View::composer('school_type.create_edit', $ns . 'SchoolTypeComposer');
    
        # 分数 - Score
        View::composer('score.index', $ns . 'ScoreIndexComposer');
        View::composer('score.create_edit', $ns . 'ScoreComposer');
        View::composer('score.stat', $ns . 'ScoreStatComposer');
        View::composer('wechat.score.analyze', $ns . 'ScoreCenterComposer');
        View::composer('wechat.score.squad', $ns . 'ScoreCenterComposer');
        View::composer('wechat.score.stat', $ns . 'ScoreCenterComposer');
        View::composer('wechat.score.student', $ns . 'ScoreCenterComposer');
    
        # 分数统计项 - ScoreRange
        View::composer('score_range.index', $ns . 'ScoreRangeIndexComposer');
        View::composer('score_range.create_edit', $ns . 'ScoreRangeComposer');
        View::composer('score_range.stat', $ns . 'ScoreRangeStatComposer');
    
        # 学期 - Semester
        View::composer('semester.index', $ns . 'SemesterIndexComposer');
        View::composer('semester.create_edit', $ns . 'SemesterComposer');
    
        # 班级 - Squad
        View::composer('class.index', $ns . 'SquadIndexComposer');
        View::composer('class.create_edit', $ns . 'SquadComposer');
    
        # 学生考勤 - StudentAttendance
        View::composer('student_attendance.index', $ns . 'StudentAttendanceIndexComposer');
        View::composer('student_attendance.stat', $ns . 'StudentAttendanceStatComposer');
        View::composer('wechat.attendance.educator', $ns . 'AttendanceEducatorComposer');
        View::composer('wechat.attendance.custodian', $ns . 'AttendanceCustodianComposer');
    
        # 学生考勤设置 - StudentAttendanceSetting
        View::composer('student_attendance_setting.create_edit', $ns . 'StudentAttendanceSettingComposer');
        View::composer('student_attendance_setting.index', $ns . 'StudentAttendanceSettingIndexComposer');
    
        # 通讯录.学生
        View::composer('student.index', $ns . 'StudentIndexComposer');
        View::composer('student.create_edit', $ns . 'StudentComposer');
        View::composer('student.show', $ns . 'StudentShowComposer');
    
        # 科目 - Subject
        View::composer('subject.index', $ns . 'SubjectIndexComposer');
        View::composer('subject.create_edit', $ns . 'SubjectComposer');
    
        # 科目次分类 - SubjectModule
        View::composer('subject_module.index', $ns . 'SubjectModuleIndexComposer');
        View::composer('subject_module.create_edit', $ns . 'SubjectModuleComposer');
    
        # 卡片 - Tab
        View::composer('tab.index', $ns . 'TabIndexComposer');
        View::composer('tab.create_edit', $ns . 'TabComposer');
    
        # 教职员工组 - Team
        View::composer('team.index', $ns . 'TeamIndexComposer');
        View::composer('team.create_edit', $ns . 'TeamComposer');
    
        # 用户中心 - User
        View::composer('user.edit', $ns . 'UserEditComposer');
        View::composer('user.reset', $ns . 'UserResetComposer');
        View::composer('user.message', $ns . 'UserMessageComposer');
        View::composer('user.event', $ns . 'UserEventComposer');
        
        # 微网站 - WapSite
        View::composer('wap_site.index', $ns . 'WapSiteIndexComposer');
        View::composer('wap_site.create_edit', $ns . 'WapSiteComposer');
        View::composer('wechat.wapsite.home', $ns . 'MobileSiteIndexComposer');
        View::composer('wechat.wapsite.module', $ns . 'MobileSiteModuleComposer');
    
        # 微网站栏目 - WapSiteModule
        View::composer('wap_site_module.index', $ns . 'WapSiteModuleIndexComposer');
        View::composer('wap_site_module.create_edit', $ns . 'WapSiteModuleComposer');
    
        # 微网站文章 - WsmArticle
        View::composer('wsm_article.index', $ns . 'WsmArticleIndexComposer');
        View::composer('wsm_article.create_edit', $ns . 'WsmArticleComposer');
    
    }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        
        //
    }
    
}
