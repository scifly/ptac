<?php
namespace App\Helpers;
/**
 * Class Snippet
 * @package App\Helpers
 */
class Snippet {
    
    # 字体颜色
    const BADGE = '<span class="%s">%s</span>';
    # Datatable
    const DT_STATUS = '<i class="fa fa-circle %s" title="%s" style="width: 20px; margin: 0 10px;"></i>';
    const DT_ANCHOR = '<a id="%s" title="%s" href="#"><i class="fa %s" style="margin-left: 15px;"></i></a>';
    /** 菜单相关 */
    const NODE_TEXT = '<span class="%s" title="%s">%s</span>%s';
    const MENU_DEFAULT_ICON = '<i class="fa fa-circle-o" style="width: 20px;"></i>';
    const MENU_ICON = '<i class="%s" style="width: 20px;"></i>';
    # 不包含子菜单的模板
    const /** @noinspection HtmlUnknownTarget */
        SIMPLE = '<li%s><a id="%s" href="%s" class="leaf"><i class="%s"></i> <span>%s</span></a></li>';
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
    const TREE_NODE = <<<HTML
        <button type="button" class="btn btn-flat" style="margin-right: 5px; margin-bottom: 5px;">
            <i class="%s">&nbsp;%s</i>&nbsp;
            <i class="fa fa-close remove-selected"></i>
            <input type="hidden" name="selectedDepartments[]" value="%s" />
        </button>
    HTML;
    # 性别符号
    const MALE = '<i class="fa fa-mars"></i>';
    const FEMALE = '<i class="fa fa-venus"></i>';
    # 卡片图标
    const ICON = '<i class="fa %s" style="width: 20px; margin: 0 5px;" title="%s"></i>&nbsp;';
    # 带图标及名称的html
    const HTML = [
        'company' => ['fa-building', 'text-blue'],
        'corp' => ['fa-weixin', 'text-green'],
        'school' => ['fa-university', 'text-purple'],
        'grade' => ['fa-object-group', ''],
        'squad' => ['fa-users', '']
    ];

    /**
     * 返回状态图标
     *
     * @param integer $status
     * @param string $enabled
     * @param string $disabled
     * @return string
     */
    static function status($status, $enabled = '已启用', $disabled = '未启用') {
        
        if (!is_numeric($status)) return $status;
        $color = 'text-' . ($status ? 'green' : 'gray');
        $text = $status ? $enabled : $disabled;
        
        return sprintf(self::DT_STATUS, $color, $text);
        
    }
    
    /**
     * 返回通讯录用户头像
     *
     * @param $d
     * @return string
     */
    static function avatar($d) {
    
        return '<img class="img-circle" style="height:16px;" src="' .
            (!empty($d) ? $d : '/img/default.png') . '"> ';
        
    }
    
    /**
     * 返回性别符号
     *
     * @param $d
     * @return string
     */
    static function gender($d) {
    
        return $d ? Snippet::MALE : Snippet::FEMALE;
        
    }
    
    /**
     * 返回带图标及名称的html
     *
     * @param $d
     * @param $type
     * @return string
     */
    static function icon($d, $type) {
        
        list($class, $color) = self::HTML[$type];
        $classes = $class . (!empty($color) ? ' ' . $color : '');
        
        return sprintf(Snippet::ICON, $classes, '') .
            '<span class="' . $color . '">' . $d . '</span>';
        
    }
    
}