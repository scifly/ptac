<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Group;
use App\Models\Menu;
use App\Models\School;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class OperatorComposer {
    
    use ModelTrait;
    
    protected $menu;
    
    function __construct(Menu $menu) {
        
        $this->menu = $menu;
        
    }
    
    public function compose(View $view) {
        
        $menuType = Menu::find($this->menu->rootMenuId(true))->menuType->name;
        $rootGId = Group::whereName('运营')->first()->id;
        $corpGId = Group::whereName('企业')->first()->id;
        $schoolGId = Group::whereName('学校')->first()->id;
        $groups = [];
        switch ($menuType) {
            case '根':
                $groups = [
                    $rootGId   => '运营',
                    $corpGId   => '企业',
                    $schoolGId => '学校',
                ];
                break;
            case '企业':
                $groups = [
                    $corpGId   => '企业',
                    $schoolGId => '学校',
                ];
                break;
            case '学校':
                $groups = [$schoolGId => '学校'];
                break;
            default:
                break;
        }
        $view->with([
            'groups'  => $groups,
            'uris'    => $this->uris(),
        ]);
        
    }
    
}