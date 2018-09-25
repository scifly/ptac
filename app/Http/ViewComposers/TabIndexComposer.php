<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Group;
use Illuminate\Contracts\View\View;

/**
 * Class TabIndexComposer
 * @package App\Http\ViewComposers
 */
class TabIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $roles = Group::whereIn('name', ['运营', '企业', '学校'])->pluck('name', 'id')->toArray();
        $optionAll = [null => '全部'];
        $view->with([
            'batch'  => true, # 需要批量操作
            'titles' => [
                '#', '名称', '控制器',
                [
                    'title' => '角色',
                    'html' => $this->singleSelectList(
                        array_merge($optionAll, $roles), 'filter_group'
                    )
                ],
                '默认功能',
                [
                    'title' => '创建于',
                    'html' => $this->inputDateTimeRange('创建于')
                ],
                [
                    'title' => '更新于',
                    'html' => $this->inputDateTimeRange('更新于')
                ],
                [
                    'title' => '类型',
                    'html' => $this->singleSelectList(
                        array_merge($optionAll, [0 => '后台', 1 => '前端', 2 => '其他']), 'filter_category'
                    )
                ],
                [
                    'title' => '状态 . 操作',
                    'html' => $this->singleSelectList(
                        array_merge($optionAll, [0 => '已禁用', 1 => '已启用']), 'filter_enabled'
                    )
                ]
            ],
            'filter' => true
        ]);
        
    }
    
}