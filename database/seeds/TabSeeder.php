<?php

use Illuminate\Database\Seeder;

class TabSeeder extends Seeder {
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('tabs')->insert([
            ['id' => 1, 'name' => 'Action类型设置', 'remark' => 'action_types', 'enabled' => 1],
            ['id' => 2, 'name' => 'Action设置', 'remark' => 'actions', 'enabled' => 1],
            ['id' => 3, 'name' => '警告类型设置', 'remark' => 'alert_types', 'enabled' => 1],
            ['id' => 4, 'name' => '微信企业应用管理', 'remark' => 'apps', 'enabled' => 1],
            ['id' => 5, 'name' => '附件类型设置', 'remark' => 'attachment_types', 'enabled' => 1],
            ['id' => 6, 'name' => '附件管理', 'remark' => 'attachments', 'enabled' => 1],
            ['id' => 7, 'name' => '考勤机管理', 'remark' => 'attendance_machines', 'enabled' => 1],
            ['id' => 8, 'name' => '班级管理', 'remark' => 'classes', 'enabled' => 1],
            ['id' => 9, 'name' => '套餐类型设置', 'remark' => 'combo_types', 'enabled' => 1],
            ['id' => 10, 'name' => '通信方式设置', 'remark' => 'comm_types', 'enabled' => 1],
            ['id' => 11, 'name' => '运营者公司管理', 'remark' => 'companies', 'enabled' => 1],
            ['id' => 12, 'name' => '与会者记录', 'remark' => 'conference_participants', 'enabled' => 1],
            ['id' => 13, 'name' => '会议队列管理', 'remark' => 'conference_queues', 'enabled' => 1],
            ['id' => 14, 'name' => '会议室设置', 'remark' => 'conference_rooms', 'enabled' => 1],
            ['id' => 15, 'name' => '企业管理', 'remark' => 'corps', 'enabled' => 1],
            ['id' => 16, 'name' => '监护人管理', 'remark' => 'custodians', 'enabled' => 1],
            ['id' => 17, 'name' => '监护人学生绑定', 'remark' => 'custodians_students', 'enabled' => 1],
            ['id' => 18, 'name' => '企业部门管理', 'remark' => 'departments', 'enabled' => 1],
            ['id' => 19, 'name' => '教职员工申诉记录', 'remark' => 'educator_appeals', 'enabled' => 1],
            ['id' => 20, 'name' => '教职员工考勤设置', 'remark' => 'educator_attendance_settings', 'enabled' => 1],
            ['id' => 21, 'name' => '教职员工考勤记录采集', 'remark' => 'educator_attendances', 'enabled' => 1],
            ['id' => 22, 'name' => '教职员工管理', 'remark' => 'educators', 'enabled' => 1],
            ['id' => 23, 'name' => '教职员工班级绑定', 'remark' => 'educators_classes', 'enabled' => 1],
            ['id' => 24, 'name' => '日程管理', 'remark' => 'events', 'enabled' => 1],
            ['id' => 25, 'name' => '考试类型设置', 'remark' => 'exam_types', 'enabled' => 1],
            ['id' => 26, 'name' => '考试管理', 'remark' => 'exams', 'enabled' => 1],
            ['id' => 27, 'name' => '年级管理', 'remark' => 'grades', 'enabled' => 1],
            ['id' => 28, 'name' => '角色管理', 'remark' => 'groups', 'enabled' => 1],
            ['id' => 29, 'name' => '专业设置', 'remark' => 'majors', 'enabled' => 1],
            ['id' => 30, 'name' => '专业科目绑定', 'remark' => 'majors_subjects', 'enabled' => 1],
            ['id' => 31, 'name' => '媒体类型设置', 'remark' => 'media_types', 'enabled' => 1],
            ['id' => 32, 'name' => '媒体管理', 'remark' => 'medias', 'enabled' => 1],
            ['id' => 33, 'name' => '菜单管理', 'remark' => 'menus', 'enabled' => 1],
            ['id' => 34, 'name' => '菜单卡片绑定', 'remark' => 'menu_tabs', 'enabled' => 1],
            ['id' => 35, 'name' => '消息类型设置', 'remark' => 'message_types', 'enabled' => 1],
            ['id' => 36, 'name' => '消息管理', 'remark' => 'messages', 'enabled' => 1],
            ['id' => 37, 'name' => '用户手机号码管理', 'remark' => 'mobiles', 'enabled' => 1],
            ['id' => 38, 'name' => '后台操作员管理', 'remark' => 'operators', 'enabled' => 1],
            ['id' => 39, 'name' => '订单管理', 'remark' => 'orders', 'enabled' => 1],
            ['id' => 40, 'name' => '调查问卷答案管理', 'remark' => 'poll_questionnaire_answers', 'enabled' => 1],
            ['id' => 41, 'name' => '调查问卷参与者查询', 'remark' => 'poll_questionnaire_participants', 'enabled' => 1],
            ['id' => 42, 'name' => '调查问卷问题选项管理', 'remark' => 'poll_questionnaire_subject_choices', 'enabled' => 1],
            ['id' => 43, 'name' => '调查问卷问题管理', 'remark' => 'poll_questionnaire_subjects', 'enabled' => 1],
            ['id' => 44, 'name' => '调查问卷管理', 'remark' => 'poll_questionnaires', 'enabled' => 1],
            ['id' => 45, 'name' => '审批流程管理', 'remark' => 'procedure_logs', 'enabled' => 1],
            ['id' => 46, 'name' => '审批流程步骤设置', 'remark' => 'procedure_steps', 'enabled' => 1],
            ['id' => 47, 'name' => '审批流程类型设置', 'remark' => 'procedure_types', 'enabled' => 1],
            ['id' => 48, 'name' => '学校类型设置', 'remark' => 'school_types', 'enabled' => 1],
            ['id' => 49, 'name' => '学校管理', 'remark' => 'schools', 'enabled' => 1],
            ['id' => 50, 'name' => '分数统计范围设置', 'remark' => 'score_ranges', 'enabled' => 1],
            ['id' => 51, 'name' => '成绩录入', 'remark' => 'scores', 'enabled' => 1],
            ['id' => 52, 'name' => '成绩统计/打印', 'remark' => 'score_totals', 'enabled' => 1],
            ['id' => 53, 'name' => '学期设置', 'remark' => 'semesters', 'enabled' => 1],
            ['id' => 54, 'name' => '教职员工短信配额管理', 'remark' => 'sms_educators', 'enabled' => 1],
            ['id' => 55, 'name' => '学校短信配额管理', 'remark' => 'sms_educators', 'enabled' => 1],
            ['id' => 56, 'name' => '学生考勤设置', 'remark' => 'student_attendance_settings', 'enabled' => 1],
            ['id' => 57, 'name' => '学生考勤记录采集', 'remark' => 'student_attendances', 'enabled' => 1],
            ['id' => 58, 'name' => '学籍管理', 'remark' => 'students', 'enabled' => 1],
            ['id' => 59, 'name' => '科目次分类设置', 'remark' => 'students', 'enabled' => 1],
            ['id' => 60, 'name' => '科目设置', 'remark' => 'subjects', 'enabled' => 1],
            ['id' => 61, 'name' => '卡片管理', 'remark' => 'tabs', 'enabled' => 1],
            ['id' => 62, 'name' => '卡片action绑定', 'remark' => 'tabs_actions', 'enabled' => 1],
            ['id' => 63, 'name' => '教职员工组别设置', 'remark' => 'teams', 'enabled' => 1],
            ['id' => 64, 'name' => '用户管理', 'remark' => 'users', 'enabled' => 1],
            ['id' => 65, 'name' => '微网站管理', 'remark' => 'wap_sites', 'enabled' => 1],
            ['id' => 66, 'name' => '微网站模块设置', 'remark' => 'wap_site_modules', 'enabled' => 1],
            ['id' => 67, 'name' => '微网站文章管理', 'remark' => 'wsm_articles', 'enabled' => 1],
        ]);
    }
}
