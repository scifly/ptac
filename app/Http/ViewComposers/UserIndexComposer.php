<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class UserIndexComposer {

    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'titles' => ['#', '用户名', '角色', '头像', '真实姓名', '性别', '电子邮件', '创建于', '更新于', '状态',],
            'uris' => $this->uris()
        ]);

    }

}