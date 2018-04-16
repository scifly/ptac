<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Group;
use App\Models\Menu;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class OperatorComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $user = Auth::user();
        $menu = new Menu();
        $menuType = Menu::find($menu->rootMenuId(true))->menuType->name;
        $rootGId = Group::whereName('运营')->first()->id;
        $corpGId = Group::whereName('企业')->first()->id;
        $schoolGId = Group::whereName('学校')->first()->id;
        $groups = [];
        $corps = $schools = null;
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
                $departmentId = $user->departments->pluck('id')->toArray()[0];
                $corp = Corp::whereDepartmentId($departmentId)->first();
                $corps = [$corp->id => $corp->name];
                if (Request::route('id')) {
                    $schools = School::whereCorpId($corp->id)->get()->pluck('name', 'id')->toArray();
                }
                break;
            case '学校':
                $groups = [$schoolGId => '学校'];
                $departmentId = $user->departments->pluck('id')->toArray()[0];
                $school = School::whereDepartmentId($departmentId)->first();
                $schools = [$school->id => $school->name];
                break;
            default:
                break;
        }
        $view->with([
            'groups'  => $groups,
            'corps'   => $corps,
            'schools' => $schools,
            'uris'    => $this->uris(),
        ]);
        
    }
    
}