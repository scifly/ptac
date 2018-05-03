<?php
namespace App\Http\ViewComposers;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Educator;
use App\Models\Group;
use App\Models\School;
use App\Models\Subject;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class EducatorComposer {
    
    use ModelTrait;
    
    protected $department;
    
    function __construct(Department $department) {
        
        $this->department = $department;
        
    }
    
    public function compose(View $view) {
        
        $schoolId = $this->schoolId();
        $school = School::find($schoolId);
        $squads = $school->classes->where('enabled', 1)
            ->pluck('name', 'id')->toArray();
        $squads[0] = '(请选择)';
        ksort($squads);
        $subjects = Subject::whereSchoolId($schoolId)
            ->where('enabled', 1)->pluck('name', 'id')
            ->toArray();
        $teams = Team::whereSchoolId($schoolId)
            ->where('enabled', 1)->pluck('name', 'id')
            ->toArray();
        $groups = Group::whereSchoolId($schoolId)
            ->orWhere('name', '学校')->where('enabled', 1)
            ->pluck('name', 'id')->toArray();
        
        $subjects[0] = '(请选择)';
        ksort($subjects);
        $mobiles = $selectedTeams = $selectedDepartmentIds = $selectedDepartments = [];
        if (Request::route('id')) {
            $selectedTeams = [];
            $educator = Educator::find(Request::route('id'));
            $mobiles = $educator->user->mobiles;
            foreach ($educator->teams as $v) {
                $selectedTeams[$v->id] = $v->name;
            }
            $selectedDepartmentIds = [];
            foreach ($educator->user->departments as $department) {
                $selectedDepartmentIds[] = $department->id;
            }
            $selectedDepartments = $this->selectedNodes($selectedDepartmentIds);
        }
        $view->with([
            'squads'                => $squads,
            'subjects'              => $subjects,
            'groups'                => $groups,
            'mobiles'               => $mobiles,
            'teams'                 => $teams,
            'selectedTeams'         => $selectedTeams,
            'selectedDepartmentIds' => implode(',', $selectedDepartmentIds),
            'selectedDepartments'   => $selectedDepartments,
            'uris'                  => $this->uris(),
        ]);
        
    }
    
    /**
     * 选中的部门节点
     *
     * @param $departmentIds
     * @return array
     */
    private function selectedNodes($departmentIds) {
        
        $departments = Department::whereIn('id', $departmentIds)->get()->toArray();
        $nodes = [];
        foreach ($departments as $department) {
            $parentId = isset($department['parent_id']) ? $department['parent_id'] : '#';
            $text = $department['name'];
            $departmentType = DepartmentType::find($department['department_type_id'])->name;
            $nodes[] = [
                'id' => $department['id'],
                'parent' => $parentId,
                'text' => $text,
                'icon' => Constant::NODE_TYPES[$departmentType]['icon'],
                'type' => Constant::NODE_TYPES[$departmentType]['type'],
            ];
        }
        
        return $nodes;
        
    }
    
}