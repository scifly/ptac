<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;

class GroupCreateComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {

//        $user = Auth::user();
//        $group = $user->group->name;
//        $schools = [];
//        switch ($group) {
//            case '运营':
//                $schools = School::whereEnabled(1)->pluck('name', 'id');
//                break;
//            case '企业':
//                $corpId = Corp::whereDepartmentId($user->topDeptId())->first()->id;
//                $schools = School::whereCorpId($corpId)->where('enabled', 1)->pluck('name', 'id');
//                break;
//            case '学校':
//                $schools = School::whereDepartmentId($user->topDeptId())
//                    ->first()->pluck('name', 'id');
//                break;
//            default:
//                break;
//        }
        $schools = School::whereId($this->schoolId())->pluck('name', 'id');
        $view->with([
            'schools' => $schools,
            'uris'    => $this->uris(),
        ]);
        
    }
    
}