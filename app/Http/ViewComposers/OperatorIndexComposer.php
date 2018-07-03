<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class OperatorIndexComposer
 * @package App\Http\ViewComposers
 */
class OperatorIndexComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'batch'  => true,
            'titles' => [
                '#', '用户名', '角色', '真实姓名', '头像', '性别',
                '电子邮件', '创建于', '更新于', '状态 . 操作',
            ],
        ]);
        
    }
    
}