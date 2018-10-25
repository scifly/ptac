<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Group;
use App\Models\Menu;
use App\Models\School;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class OperatorComposer
 * @package App\Http\ViewComposers
 */
class OperatorComposer {
    
    use ModelTrait;
    
    protected $menu;
    
    /**
     * OperatorComposer constructor.
     * @param Menu $menu
     */
    function __construct(Menu $menu) {
        
        $this->menu = $menu;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        /**
         * @param array $names
         * @return array
         */
        function groups(array $names) {
            return Group::whereIn('name', $names)->pluck('name', 'id')->toArray();
        }
        
        $operator = $departmentId = $corps = $schools = null;
        $rootMenu = Menu::find($this->menu->rootMenuId(true));
        $groups = [];
        if (Request::route('id')) {
            $operator = User::find(Request::route('id'));
            $departmentId = $this->head($operator);
        }
        switch ($rootMenu->menuType->name) {
            case '根':
                $groups = groups(['运营', '企业', '学校']);
                if (Request::route('id')) {
                    $role = $operator->role();
                    if ($role != '运营') {
                        $corps = Corp::all()->pluck('name', 'id')->toArray();
                    }
                    if ($role == '学校') {
                        $schools = School::whereCorpId($operator->educator->school->corp_id)
                            ->pluck('name', 'id')->toArray();
                    }
                }
                break;
            case '企业':
                $groups = groups(['企业', '学校']);
                $corp = null;
                if (Request::route('id')) {
                    switch ($operator->role()) {
                        case '企业':
                            $corp = Corp::whereDepartmentId($departmentId)->first();
                            break;
                        case '学校':
                            $corpId = $operator->educator->school->corp_id;
                            $corp = Corp::find($corpId);
                            $schools = School::whereCorpId($corpId)
                                ->pluck('name', 'id')->toArray();
                            break;
                        default:
                            break;
                    }
                } else {
                    $corp = Corp::whereMenuId($rootMenu->id)->first();
                }
                $corps = [$corp->id => $corp->name];
                break;
            case '学校':
                $groups = groups(['学校']);
                if (Request::route('id')) {
                    $school = School::whereDepartmentId($departmentId)->first();
                } else {
                    $school = School::whereMenuId($rootMenu->id)->first();
                }
                $corp = Corp::find($school->corp_id);
                $corps = [$corp->id => $corp->name];
                $schools = [$school->id => $school->name];
                break;
            default:
                break;
        }
        $view->with([
            'mobiles' => Request::route('id') ? User::find(Request::route('id'))->mobiles : [],
            'groups'  => $groups,
            'corps'   => $corps,
            'schools' => $schools,
        ]);
        
    }
    
}