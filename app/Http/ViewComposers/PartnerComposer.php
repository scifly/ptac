<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class OperatorIndexComposer
 * @package App\Http\ViewComposers
 */
class PartnerComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'batch'  => true,
            'titles' => [
                '#', '全称', '接口用户名', '接口密码', '联系人',
                '电子邮箱', '创建于', '更新于', '状态',
            ],
        ]);
        
    }
    
}