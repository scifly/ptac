<?php

namespace App\Http\ViewComposers;

use App\Models\Corp;
use App\Models\Group;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class OperatorCreateComposer {

    protected $group;

    public function __construct(Group $group) {

        $this->group = $group;

    }

    public function compose(View $view) {

        # 系统管理员角色(运营, 企业, 学校)的school_id均为NULL
        $user = Auth::user();
        switch ($user->group->name) {
            case '运营':
                $groups = $this->group->where('school_id', null)
                    ->pluck('name', 'id');
                $corps = Corp::pluck('name', 'department_id');
                $schools = School::pluck('name', 'department_id');
                $view->with(['groups' => $groups, 'corps' => $corps, 'schools' => $schools]);
                break;
            case '企业':
                $groups = $this->group->where('school_id', null)
                    ->whereIn('name', ['企业', '学校'])->pluck('name', 'id');
                $rootId = $user->topDeptId($user);
                $corpId = Corp::whereDepartmentId($rootId)->first()->id;
                $schools = School::whereCorpId($corpId)->pluck('name', 'department_id');
                $view->with(['groups' => $groups, 'rootId' => $rootId, 'schools' => $schools]);
                break;
            case '学校':
                $groups = $this->group->where('school_id', null)
                    ->where('name', '学校')->pluck('name', 'id');
                $view->with(['groups' => $groups, 'rootId' => $user->topDeptId($user)]);
                break;
            default:
                break;
        }

    }

}