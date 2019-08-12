<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class OperatorComposer
 * @package App\Http\ViewComposers
 */
class OperatorComposer {
    
    use ModelTrait;
    
    protected $user;
    
    /**
     * OperatorComposer constructor.
     * @param User $user
     */
    function __construct(User $user) {
        
        $this->user = $user;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'batch'  => true,
                'titles' => [
                    '#', '用户名', '角色', '真实姓名', '头像', '性别',
                    '电子邮件', '创建于', '更新于', '状态 . 操作',
                ],
            ];
        } else {
            $data = array_combine(
                ['mobiles', 'groups', 'corps', 'schools'],
                $this->user->compose()
            );
        }
        
        $view->with($data);
        
    }
    
}