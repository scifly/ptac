<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

/**
 * Class PassageRuleIndexComposer
 * @package App\Http\ViewComposers
 */
class PassageRuleIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => [
                '#', '名称', '规则id',
                [
                    'title' => '起始日期',
                    'html' => $this->inputDateTimeRange('起始日期', false)
                ],
                [
                    'title' => '结束日期',
                    'html' => $this->inputDateTimeRange('结束日期', false)
                ],
                '适用范围', '时段1', '时段2', '时段3', '关联规则id',
                [
                    'title' => '状态 . 操作',
                    'html' => $this->singleSelectList(
                        [null => '全部'] + [0 => '禁用', 1 => '启用'], 'filter_enabled'
                    )
                ]
            ],
        ]);
        
    }
    
}