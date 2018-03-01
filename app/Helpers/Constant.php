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
    
    # 性别符号
    const MALE = '<i class="fa fa-mars"></i>';
    const FEMALE = '<i class="fa fa-venus"></i>';
    
    const SIMPLE = '<li%s><a id="%s" href="%s" class="leaf"><i class="%s"></i> %s</a></li>';
    # 包含子菜单的HTML模板
    const TREE = <<<HTML
            <li class="treeview%s">
                <a href="#">
                    <i class="%s"></i> <span>%s</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
HTML;
}