<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\ActionType;
use Illuminate\Contracts\View\View;

/**
 * Class ActionIndexComposer
 * @package App\Http\ViewComposers
 */
class ActionIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $optionAll = [null => '全部'];
        $view->with([
            'titles' => [
                '#', '名称', '方法', '路由', '控制器',
                'View路径', 'js路径',
                [
                    'title' => '请求类型',
                    'html' => $this->singleSelectList(
                        $optionAll + ActionType::pluck('name', 'id')->toArray(),
                        'filter_action_type'
                    )
                ],
                [
                    'title' => '功能类型',
                    'html' => $this->singleSelectList(
                        array_merge($optionAll, [0 => '后台', 1 => '前端', 2 => '其他']),
                        'filter_category'
                    )
                ],
                [
                    'title' => '状态 . 操作',
                    'html' => $this->singleSelectList(
                        array_merge($optionAll, [0 => '已禁用', 1 => '已启用']),
                        'filter_enabled'
                    )
                ]
            ],
            'filter' => true,
        ]);
        
    }
    
}