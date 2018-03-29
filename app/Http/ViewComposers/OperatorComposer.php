<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Group;
use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class OperatorComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
    
        $menu = new Menu();
        $menuType = Menu::find($menu->rootMenuId(true))->menuType->name;
        $rootGId = Group::whereName('运营')->first()->id;
        $corpGId = Group::whereName('企业')->first()->id;
        $schoolGId = Group::whereName('学校')->first()->id;
    
        $groups = [];
        switch ($menuType) {
            case '根':
                $groups = [
                    $rootGId => '运营',
                    $corpGId => '企业',
                    $schoolGId => '学校'
                ];
                break;
            case '企业':
                $groups = [
                    $corpGId => '企业',
                    $schoolGId => '学校'
                ];
                break;
            case '学校':
                $groups = [$schoolGId => '学校'];
                break;
            default:
                break;
        }
        $view->with([
            'groups' => $groups,
            'uris' => $this->uris()
        ]);
        
    }

}