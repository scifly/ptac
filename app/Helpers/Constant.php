<?php
namespace App\Helpers;

class Constant {
    
    # 状态
    const DISABLED = 0;
    const ENABLED = 1;
    
    # 角色Id
    const SHARED = 0;
    const ROOT = 1;
    const CORP = 2;
    const SCHOOL = 3;
    
    const SUPER_ROLES = ['运营', '企业', '学校'];

    const BATCH_OPERATIONS = ['enable', 'disable', 'delete'];
    
    # 企业管理员可访问的运营类功能
    const ALLOWED_CORP_ACTIONS = [
        'corps/edit/%s',
        'corps/update/%s',
    ];
    # 校级管理员可访问的企业类功能
    const ALLOWED_SCHOOL_ACTIONS = [
        'schools/show/%s',
        'schools/edit/%s',
        'schools/update/%s',
    ];
    const ALLOWED_WAPSITE_ACTIONS = [
        'wap_sites/show/%s',
        'wap_sites/edit/%s',
        'wap_sites/update/%s',
    ];
    
    const WEEK_DAYS = [
        '星期日', '星期一', '星期二', '星期三',
        '星期四', '星期五', '星期六'
    ];
    
    const SYNC_ACTIONS = [
        'create' => '创建',
        'update' => '更新',
        'delete' => '删除',
    ];
    
    const ROOT_DEPARTMENT_ID = 1;
    
    const DEPARTMENT_TYPES = [
        '根' => 'root',
        '运营' => 'company',
        '企业' => 'corp',
        '学校' => 'school',
        '年级' => 'grade',
        '班级' => 'class',
        '其他' => 'other'
    ];
    
    const MENU_TYPES = [
        '根' => 'root',
        '运营' => 'company',
        '企业' => 'corp',
        '学校' => 'school',
        '其他' => 'other'
    ];
    
    const NODE_TYPES = [
        '根'  => ['color' => 'text-gray', 'type' => 'root', 'icon' => 'fa fa-sitemap'],
        '运营' => ['color' => 'text-blue', 'type' => 'company', 'icon' => 'fa fa-building'],
        '企业' => ['color' => 'text-green', 'type' => 'corp', 'icon' => 'fa fa-weixin'],
        '学校' => ['color' => 'text-purple', 'type' => 'school', 'icon' => 'fa fa-university'],
        '年级' => ['color' => 'text-black', 'type' => 'grade', 'icon' => 'fa fa-object-group'],
        '班级' => ['color' => 'text-black', 'type' => 'class', 'icon' => 'fa fa-users'],
        '其他' => ['color' => 'text-black', 'type' => 'other', 'icon' => 'fa fa-list'],
    ];
    
    # 控制器相对路径
    const CONTROLLER_DIR = 'app/Http/Controllers';

    const EXCLUDED_CONTROLLERS = [
        'ApiController',
        'AttendanceController',
        'Controller',
        'ForgotPasswordController',
        'HomeController',
        'HomeWorkController',
        'LoginController',
        'MessageCenterController',
        'MobileSiteController',
        'RegisterController',
        'ResetPasswordController',
        'ScoreCenterController',
        'SyncController',
        'TestController',
    ];
    
    const INFO_TYPES = [
        'text' => '文本',
        'image' => '图片',
        'voice' => '语音',
        'video' => '视频',
        'file' => '文件',
        'textcard' => '卡片',
        'mpnews' => '图文',
        'sms' => '短信',
        'other' => '未知'
    ];
    
    const APPS = [
        'at' => '考勤中心',
        'mc' => '消息中心',
        'sc' => '成绩中心',
        'ws' => '微网站'
    ];
    
}