<?php

namespace App\Http\ViewComposers;

use App\Models\Corp;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class GroupCreateComposer {

    protected $school;

    public function __construct(School $school) {

        $this->school = $school;

    }

    public function compose(View $view) {

        $user = Auth::user();
        $group = $user->group->name;
        $schools = [];
        switch ($group) {
            case '运营':
                $schools = School::whereEnabled(1)->pluck('id', 'name');
                break;
            case '企业':
                $corpId = Corp::whereDepartmentId($user->topDeptId())->first()->id;
                $schools = School::whereCorpId($corpId)->where('enabled', 1)->pluck('id', 'name');
                break;
            case '学校':
                $schools = School::whereDepartmentId($user->topDeptId())
                    ->first()->pluck('id', 'name');
                break;
            default:
                break;
        }
        $view->with(['schools' => $schools]);

    }

}