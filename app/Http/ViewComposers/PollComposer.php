<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

/**
 * Class PollComposer
 * @package App\Http\ViewComposers
 */
class PollComposer {
    
    use ModelTrait;
    
    /** @param View $view */
    public function compose(View $view) {
    
        $view->with([
            'titles' => [
                '#', '名称', '发布者', '开始时间', '结束时间',
                ['title' => '创建于', 'html' => $this->htmlDTRange('创建于')],
                ['title' => '更新于', 'html' => $this->htmlDTRange('更新于')],
                [
                    'title' => '状态 . 操作',
                    'html' => $this->htmlDTRange(
                        collect([null => '全部'])->union(['待发布', '已发布'])
                    )
                ]
            ],
            'batch' => true,
            'filter' => true,
        ]);
        
    }
    
}