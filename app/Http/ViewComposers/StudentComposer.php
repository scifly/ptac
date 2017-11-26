<?php
namespace App\Http\ViewComposers;

use App\Models\Company;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class StudentComposer {
    
    protected $user, $department;
    
    public function __construct(User $user, Department $department) {
        
        $this->user = $user;
        $this->department = $department;
        
    }
    
    public function compose(View $view) {

        $user = Auth::user();
        $companies = null;
        $corps = null;
        $schools = null;
        $grades = null;
        $classes = null;
        switch ($user->group->name) {
            case '运营':
                $companies = Company::whereEnabled(1)
                    ->pluck('name', 'id')
                    ->toArray();
                $corps = Corp::whereEnabled(1)
                    ->where('company_id', array_keys($companies)[0])
                    ->pluck('name', 'id')
                    ->toArray();
                $schools = School::whereEnabled(1)
                    ->where('corp_id', array_keys($corps)[0])
                    ->pluck('name', 'id')
                    ->toArray();
                $grades = Grade::whereEnabled(1)
                    ->where('school_id', array_keys($schools)[0])
                    ->pluck('name', 'id')
                    ->toArray();
                $classes = Squad::whereEnabled(1)
                    ->where('grade_id', array_keys($grades)[0])
                    ->pluck('name', 'id')
                    ->toArray();
                break;
            case '企业':
                $corp = Corp::whereDepartmentId($this->user->topDeptId($user))->first();
                $corps = [$corp['id'] => $corp['name']];
                $schools = School::whereEnabled(1)
                    ->where('corp_id', $corp['id'])
                    ->pluck('name', 'id')
                    ->toArray();
                $grades = Grade::whereEnabled(1)
                    ->where('school_id', array_keys($schools)[0])
                    ->pluck('name', 'id')
                    ->toArray();
                $classes = Squad::whereEnabled(1)
                    ->where('grade_id', array_keys($grades)[0])
                    ->pluck('name', 'id')
                    ->toArray();
                break;
            case '学校':
                $school = School::whereDepartmentId($this->user->topDeptId($user))->first();
                $schools = [$school['id'] => $school['name']];
                $grades = Grade::whereEnabled(1)
                    ->where('school_id', array_keys($schools)[0])
                    ->pluck('name', 'id')
                    ->toArray();
                $classes = Squad::whereEnabled(1)
                    ->where('grade_id', array_keys($grades)[0])
                    ->pluck('name', 'id')
                    ->toArray();
                break;
            default:
                $topDeptId = $this->user->topDeptId($user);
                if ($department = $this->department->topDepartment($topDeptId)) {
                    switch ($department['type']) {
                        case '学校':
                            $schools = $department['department'];
                            $grades = Grade::whereEnabled(1)
                                ->where('school_id', array_keys($schools)[0])
                                ->pluck('name', 'id')
                                ->toArray();
                            $classes = Squad::whereEnabled(1)
                                ->where('grade_id', array_keys($grades)[0])
                                ->pluck('name', 'id')
                                ->toArray();
                            break;
                        case '年级':
                            $grades = $department['department'];
                            $classes = Squad::whereEnabled(1)
                                ->where('grade_id', array_keys($grades)[0])
                                ->pluck('name', 'id')
                                ->toArray();
                            break;
                        case '班级':
                            $classes = $department['department'];
                            break;
                        default: break;
                    }
                }
                break;
        }
        $view->with([
            'companies'  => $companies,
            'corps'      => $corps,
            'schools'    => $schools,
            'grades'     => $grades,
            'classes'    => $classes,
        ]);
        
    }
    
}