<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
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
        
        $view->with([
            'titles' => [
                '#', '名称', '方法', '路由', '控制器',
                'View路径', 'js路径', '请求类型',
                [
                    'title' => '状态 . 操作',
                    'html' => $this->singleSelectList(
                        [null => '全部', 0 => '已禁用', 1 => '已启用'], 'filter_enabled'
                    )
                ]
            ],
            'filter' => true,
        ]);
        
    }
    
}