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
    
    const DAYS = [
        '星期日', '星期一', '星期二', '星期三',
        '星期四', '星期五', '星期六'
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
        'TestController',
    ];
    
}