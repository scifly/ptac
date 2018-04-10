<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class ExamIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => [
                '#', '名称', '备注', '考试类型', '科目满分', '科目及格分数',
                '考试开始日期', '考试结束日期', '创建于', '更新于', '状态 . 操作',
            ],
            'uris'   => $this->uris(),
        ]);
        
    }
    
}