<?php
namespace App\Helpers;

class Snippet {
    
    # 字体颜色
    const BADGE_COLOR = '<span class="%s">%s</span>';
    const BADGE_GRAY = '<span class="text-gray">%s</span>';
    const BADGE_GREEN = '<span class="text-green">%s</span>';
    const BADGE_YELLOW = '<span class="text-yellow">%s</span>';
    const BADGE_RED = '<span class="text-red">%s</span>';
    const BADGE_LIGHT_BLUE = '<span class="text-light-blue">%s</span>';
    const BADGE_MAROON = '<span class="text-maroon">%s</span>';
    const BADGE_AQUA = '<span class="text-aqua">%s</span>';
    const BADGE_BLACK = '<span class="text-black">%s</span>';
    const BADGE_DANGER = '<span class="text-danger">%s</span>';
    const BADGE_FUCHSIA = '<span class="text-fuchsia">%s</span>';
    
    # Datatable
    const DT_ON = '<i class="fa fa-circle text-green" title="已启用"></i>';
    const DT_OFF = '<i class="fa fa-circle text-gray" title="未启用"></i>';
    const DT_LINK_EDIT = '<a id="%s" title="编辑" href="#"><i class="fa fa-pencil"></i></a>';
    const DT_LINK_DEL = '<a id="%s" title="删除" data-toggle="modal"><i class="fa fa-remove"></i></a>';
    const DT_LINK_SHOW = '<a id="%s" title="详情" data-toggle="modal"><i class="fa fa-bars"></i></a>';
    const DT_SPACE = '&nbsp;';
    const DT_PRIMARY = '<span class="badge badge-info">%s</span>';
    const DT_LINK_RECHARGE = '<a id="%s" title="充值" href="#"><i class="fa fa-money"></i></a>';
    const DT_LOCK = '<i class="fa fa-lock"></i>&nbsp;已占用';
    const DT_UNLOCK = '<i class="fa fa-unlock"></i>&nbsp;空闲中';
    
    /** 菜单相关 */
    const MENU_TEXT = '<span style="color: %s;">%s</span>';
    const MENU_DEFAULT_ICON = '<i class="fa fa-circle-o" style="width: 20px;"></i>';
    const MENU_ICON = '<i class="%s" style="width: 20px;"></i>';
    # 不包含子菜单的模板
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
    
    # 性别符号
    const MALE = '<i class="fa fa-mars"></i>';
    const FEMALE = '<i class="fa fa-venus"></i>';
    
    # 卡片图标
    const ICON = '<i class="fa %s" style="width: 20px;"></i>&nbsp;';
    
}