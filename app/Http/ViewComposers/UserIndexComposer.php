<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class UserIndexComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'titles' => ['#', '用户名', '角色', '头像', '真实姓名', '性别', '电子邮件', '创建于', '更新于', '状态 . 操作',],
        ]);
        
    }
    
}