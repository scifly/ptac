<?php
namespace App\Helpers;

class Snippet {
    
    const BADGE_GRAY = '<span class="text-black">[n/a]</span>';
    const BADGE_GREEN = '<span class="text-green">%s</span>';
    const BADGE_YELLOW = '<span class="text-yellow">%s</span>';
    const BADGE_RED = '<span class="text-red">%s</span>';
    const BADGE_LIGHT_BLUE = '<span class="text-light-blue">%s</span>';
    const BADGE_MAROON = '<span class="text-maroon">%s</span>';
    
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
    
}