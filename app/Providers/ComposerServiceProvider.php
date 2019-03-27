<?php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Class ComposerServiceProvider
 * @package App\Providers
 */
class ComposerServiceProvider extends ServiceProvider {
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
    
        $composers = [
            # 系统参数
            'init.index'                              => 'Init',
            # 功能 - Action
            // 'action' => ['index', 'create_edit'],
            'action.index'                            => 'ActionIndex',
            'action.create_edit'                      => 'Action',
            # 功能类型 - ActionType
            'action_type.index'                       => 'ActionTypeIndex',
            'action_type.create_edit'                 => 'ActionType',
            # 警告类型 - AlertType
            'alert_type.index'                        => 'AlertTypeIndex',
            'alert_type.create_edit'                  => 'AlertType',
            # 企业应用 - App
            'app.index'                               => 'AppIndex',
            'app.edit'                                => 'App',
            # 附件类型 - AttachmentType
            'attachment_type.index'                   => 'AttachmentTypeIndex',
            'attachment_type.create_edit'             => 'AttachmentType',
            # 门禁设备 - Turnstile
            'turnstile.index'                         => 'TurnstileIndex',
            # 一卡通 - Card
            'card.index'                              => 'CardIndex',
            'card.create_edit'                        => 'Card',
            # 通信类型 - CommType
            'comm_type.index'                         => 'CommTypeIndex',
            'comm_type.create_edit'                   => 'CommType',
            # 套餐类型 - ComboType
            'combo_type.index'                        => 'ComboTypeIndex',
            'combo_type.create_edit'                  => 'ComboType',
            # 运营者 - Company
            'company.index'                           => 'CompanyIndex',
            'company.create_edit'                     => 'Company',
            # 与会者 - ConferenceParticipant
            'conference_participant.index'            => 'ConferenceParticipantIndex',
            # 会议 - ConferenceQueue
            'conference_queue.index'                  => 'ConferenceQueueIndex',
            'conference_queue.create_edit'            => 'ConferenceQueue',
            'conference_queue.edit'                   => 'ConferenceQueueEdit',
            # 会议室 - ConferenceRoom
            'conference_room.index'                   => 'ConferenceRoomIndex',
            'conference_room.create_edit'             => 'ConferenceRoom',
            # 学生消费 - Consumption
            'consumption.index'                       => 'ConsumptionIndex',
            'consumption.show'                        => 'ConsumptionStat',
            # 微信企业 - Corp
            'corp.index'                              => 'CorpIndex',
            'corp.create_edit'                        => 'Corp',
            # 通讯录.监护人 - Custodian
            'custodian.index'                         => 'CustodianIndex',
            'custodian.create'                        => 'Custodian',
            'custodian.edit'                          => 'Custodian',
            'custodian.relationship'                  => 'CustodianRelationship',
            'custodian.issue'                         => 'CustodianIssue',
            'custodian.permit'                        => 'CustodianPermit',
            # 部门 - Department
            'department.index'                        => 'DepartmentIndex',
            'department.create_edit'                  => 'Department',
            # 部门类型 - DepartmentType
            'department_type.index'                   => 'DepartmentTypeIndex',
            'department_type.create_edit'             => 'DepartmentType',
            # 教职员工考勤 - EducatorAttendance
            'educator_attendance.index'               => 'EducatorAttendanceIndex',
            'educator_attendance.stat'                => 'EducatorAttendanceStat',
            # 教职员工考勤设置 - EducatorAttendanceSetting
            'educator_attendance_setting.index'       => 'EducatorAttendanceSettingIndex',
            'educator_attendance_setting.create_edit' => 'EducatorAttendanceSetting',
            # 通讯录.教职员工 - Educator
            'educator.index'                          => 'EducatorIndex',
            'educator.create'                         => 'Educator',
            'educator.edit'                           => 'Educator',
            'educator.recharge'                       => 'EducatorRecharge',
            'educator.issue'                          => 'EducatorIssue',
            'educator.permit'                         => 'EducatorPermit',
            # 日历 - Event
            'event.index'                             => 'EventIndex',
            'event.show'                              => 'Event',
            # 考试 - Exam
            'exam.index'                              => 'ExamIndex',
            'exam.create_edit'                        => 'Exam',
            'exam.show'                               => 'ExamShow',
            # 考试类型 - ExamType
            'exam_type.index'                         => 'ExamTypeIndex',
            'exam_type.create_edit'                   => 'ExamType',
            # 年级 - Grade
            'grade.index'                             => 'GradeIndex',
            'grade.create_edit'                       => 'Grade',
            # 角色/权限 - Group
            'group.index'                             => 'GroupIndex',
            'group.create_edit'                       => 'Group',
            'group.create'                            => 'GroupCreate',
            'group.edit'                              => 'GroupEdit',
            # 图标 - Icon
            'icon.index'                              => 'IconIndex',
            'icon.create_edit'                        => 'Icon',
            # 专业 - Major
            'major.index'                             => 'MajorIndex',
            'major.create_edit'                       => 'Major',
            # 媒体类型 - MediaType
            'media_type.create_edit'                  => 'MediaType',
            'media_type.index'                        => 'MediaTypeIndex',
            # 菜单 - Menu
            'menu.index'                              => 'MenuIndex',
            'menu.create_edit'                        => 'Menu',
            'menu.sort'                               => 'MenuSort',
            # 菜单类型 - MenuType
            'menu_type.index'                         => 'MenuTypeIndex',
            'menu_type.create_edit'                   => 'MenuType',
            # 消息中心 - MessageCenter
            'wechat.message_center.index'             => 'MessageCenterIndex',
            'wechat.message_center.create_edit'       => 'MessageCenter',
            'wechat.message_center.show'              => 'MessageCenterShow',
            # 消息 - Message
            'message.index'                           => 'MessageIndex',
            # 消息类型 - MessageType
            'message_type.index'                      => 'MessageTypeIndex',
            'message_type.create_edit'                => 'MessageTypeIndex',
            # 应用模块 - Module
            'module.index'                            => 'ModuleIndex',
            'module.create_edit'                      => 'Module',
            # 超级用户 - Operator
            'operator.index'                          => 'OperatorIndex',
            'operator.create_edit'                    => 'Operator',
            # 通行记录 - PassageLog
            'passage_log.index'                       => 'PassageLogIndex',
            # 通行规则 - PassageRule
            'passage_rule.index'                      => 'PassageRuleIndex',
            'passage_rule.create_edit'                => 'PassageRule',
            # 合作伙伴 - Partner
            'partner.index'                           => 'PartnerIndex',
            # 投票问卷 - PollQuestionnaire
            'poll_questionnaire.index'                => 'PollQuestionnaireIndex',
            'poll_questionnaire.create_edit'          => 'PollQuestionnaire',
            # 投票问卷问题选项 - PollQuestionnaireSubjectChoice
            'pq_choice.create_edit'                   => 'PqChoice',
            'pq_choice.index'                         => 'PqChoiceIndex',
            # 投票问卷题目 - PollQuestionnaireSubject
            'pq_subject.create_edit'                  => 'PqSubject',
            'pq_subject.index'                        => 'PqSubjectIndex',
            # 审批流程 - Procedure
            'procedure.index'                         => 'ProcedureIndex',
            'procedure.create_edit'                   => 'Procedure',
            # 审批流程步骤 - ProcedureStep
            'procedure_step.index'                    => 'ProcedureStepIndex',
            'procedure_step.create_edit'              => 'ProcedureStep',
            # 审批流程类型 - ProcedureType
            'procedure_type.index'                    => 'ProcedureTypeIndex',
            'procedure_type.create_edit'              => 'ProcedureType',
            # 学校 - School
            'school.index'                            => 'SchoolIndex',
            'school.create_edit'                      => 'School',
            'school.show'                             => 'SchoolShow',
            # 学校类型 - SchoolType
            'school_type.index'                       => 'SchoolTypeIndex',
            'school_type.create_edit'                 => 'SchoolType',
            # 分数 - Score
            'score.index'                             => 'ScoreIndex',
            'score.create_edit'                       => 'Score',
            'score.stat'                              => 'ScoreStat',
            'wechat.score.analyze'                    => 'ScoreCenter',
            'wechat.score.squad'                      => 'ScoreCenter',
            'wechat.score.stat'                       => 'ScoreCenter',
            'wechat.score.student'                    => 'ScoreCenter',
            # 总分 - ScoreTotal
            'score_total.index'                       => 'ScoreTotalIndex',
            # 分数统计项 - ScoreRange
            'score_range.index'                       => 'ScoreRangeIndex',
            'score_range.create_edit'                 => 'ScoreRange',
            'score_range.stat'                        => 'ScoreRangeStat',
            # 学期 - Semester
            'semester.index'                          => 'SemesterIndex',
            'semester.create_edit'                    => 'Semester',
            # 班级 - Squad
            'class.index'                             => 'SquadIndex',
            'class.create_edit'                       => 'Squad',
            # 学生考勤 - StudentAttendance
            'student_attendance.index'                => 'StudentAttendanceIndex',
            'student_attendance.stat'                 => 'StudentAttendanceStat',
            'wechat.attendance.educator'              => 'AttendanceEducator',
            'wechat.attendance.custodian'             => 'AttendanceCustodian',
            # 学生考勤设置 - StudentAttendanceSetting
            'student_attendance_setting.create_edit'  => 'StudentAttendanceSetting',
            'student_attendance_setting.index'        => 'StudentAttendanceSettingIndex',
            # 通讯录 - Student
            'student.index'                           => 'StudentIndex',
            'student.create'                          => 'Student',
            'student.edit'                            => 'Student',
            'student.issue'                           => 'StudentIssue',
            'student.permit'                          => 'StudentPermit',
            # 科目 - Subject
            'subject.index'                           => 'SubjectIndex',
            'subject.create_edit'                     => 'Subject',
            # 科目次分类 - SubjectModule
            'subject_module.index'                    => 'SubjectModuleIndex',
            'subject_module.create_edit'              => 'SubjectModule',
            # 卡片 - Tab
            'tab.index'                               => 'TabIndex',
            'tab.create_edit'                         => 'Tab',
            # 标签 - Tag
            'tag.index'                               => 'TagIndex',
            'tag.create_edit'                         => 'Tag',
            # 用户中心 - User
            'user.edit'                               => 'UserEdit',
            'user.reset'                              => 'UserReset',
            'user.message'                            => 'UserMessage',
            'user.event'                              => 'UserEvent',
            # 微网站 - WapSite
            'wap_site.index'                          => 'WapSiteIndex',
            'wap_site.create_edit'                    => 'WapSite',
            'wechat.wapsite.home'                     => 'MobileSiteIndex',
            'wechat.wapsite.module'                   => 'MobileSiteModule',
            # 微网站栏目 - WapSiteModule
            'wap_site_module.index'                   => 'WapSiteModuleIndex',
            'wap_site_module.create_edit'             => 'WapSiteModule',
            # 微网站文章 - WsmArticle
            'wsm_article.index'                       => 'WsmArticleIndex',
            'wsm_article.create_edit'                 => 'WsmArticle',
        ];
        foreach ($composers as $view => $composer) {
            View::composer($view, 'App\Http\ViewComposers\\' . $composer . 'Composer');
        }
    
    }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() { }
    
}
