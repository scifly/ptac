<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

/**
 * Class CardIndexComposer
 * @package App\Http\ViewComposers
 */
class CardIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => [
                '#', '姓名', '卡号',
                [
                    'title' => '发卡时间',
                    'html'  => $this->inputDateTimeRange('发卡时间')
                ],
                [
                    'title' => '更新于',
                    'html'  => $this->inputDateTimeRange('更新于')
                ],
                [
                    'title' => '状态 . 操作',
                    'html' => $this->singleSelectList(
                        [null => '全部', 0 => '待发', 1 => '正常', 2 => '挂失'], 'filter_status'
                    )
                ],
            ],
        ]);
        
    }
    
}