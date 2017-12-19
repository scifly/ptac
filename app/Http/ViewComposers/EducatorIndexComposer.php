<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class EducatorIndexComposer {
    use ControllerTrait;
    protected $user;

    public function __construct(User $user) {

        $this->user = $user;

    }

    public function compose(View $view) {

        $schools = null;
        $grades = null;
        $classes = null;
        $user = Auth::user();
        if ($user->educator) {
            $schools = School::whereId($user->educator->school_id)
                ->where('enabled', 1)
                ->pluck('name', 'id');
        } else {
            $topDepartmentId = $this->user->topDeptId($user);
            $departmentType = Department::whereId($topDepartmentId)->first()->departmentType;
            switch ($departmentType->name) {
                case '根':
                case '运营':
                    $schools = School::all()
                        ->where('enabled', 1)
                        ->pluck('name', 'id');
                    break;
                case '企业':
                    $corpId = Corp::whereDepartmentId($topDepartmentId)->first()->id;
                    $schools = School::whereCorpId($corpId)
                        ->where('enabled', 1)
                        ->pluck('name', 'id');
                    break;
                case '学校':
                    $schools = School::whereDepartmentId($topDepartmentId)
                        ->where('enabled', 1)
                        ->pluck('name', 'id');
                    break;
                default:
                    break;
            }
        }
        if ($schools) {
            $grades = Grade::whereSchoolId($schools->keys()->first())
                ->where('enabled', 1)
                ->pluck('name', 'id');
        }
        if ($grades) {
            $classes = Squad::whereGradeId($grades->keys()->first())
                ->where('enabled', 1)
                ->pluck('name', 'id');
        }
        $view->with([
            'schools' => $schools,
            'grades' => $grades,
            'classes' => $classes,
            'uris' => $this->uris()
        ]);
    }

}