<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class OperatorIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'batch' => true,
            'titles' => [
                '#', '用户名', '角色', '头像', '真实姓名',
                '性别', '电子邮件', '创建于', '更新于', '状态 . 操作',
            ],
            // 'uris'   => $this->uris(),
        ]);
        
    }
    
}