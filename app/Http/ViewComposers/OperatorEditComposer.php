<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Group;
use App\Models\Operator;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class OperatorEditComposer {
    use ModelTrait;

    public function compose(View $view) {

        $operator = Operator::find(Request::route('id'));
        $user = Auth::user();
        switch ($user->group->name) {
            case '运营':
                // $groups = Group::whereSchoolId(null)->pluck('name', 'id');
                $groups = Group::whereSchoolId(null)->where('name','企业')->pluck('name', 'id');

                $corps = Corp::pluck('name', 'department_id');
                $schools = School::pluck('name', 'department_id');
                $view->with(['groups' => $groups]);
                break;
            case '企业':
                $groups = Group::whereSchoolId(null)
                    ->whereIn('name', ['企业', '学校'])->pluck('name', 'id');
                $rootId = $user->topDeptId();
                $corpId = Corp::whereDepartmentId($rootId)->first()->id;
                $schools = School::whereCorpId($corpId)->pluck('name', 'department_id');
                $view->with(['groups' => $groups, 'rootId' => $rootId, 'schools' => $schools, 'uris' => $this->uris()]);
                break;
            case '学校':
                $groups = Group::whereSchoolId(null)
                    ->where('name', '学校')->pluck('name', 'id');
                $view->with(['groups' => $groups, 'rootId' => $user->topDeptId(), 'uris' => $this->uris()]);
                break;
            default:
                break;
        }
        $selectedDepartmentIds = $operator->user->departments->pluck('id')->toArray();
        $view->with([
            'selectedDepartmentIds' => implode(',', $selectedDepartmentIds),
            'selectedDepartments' => Department::selectedNodes($selectedDepartmentIds),
            'mobiles' => $operator->user->mobiles,
            'uris' => $this->uris()
        ]);

    }

}