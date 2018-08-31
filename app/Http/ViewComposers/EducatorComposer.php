<?php
namespace App\Http\ViewComposers;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Educator;
use App\Models\Group;
use App\Models\Squad;
use App\Models\Subject;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class EducatorComposer
 * @package App\Http\ViewComposers
 */
class EducatorComposer {
    
    use ModelTrait;
    
    protected $department;
    
    /**
     * EducatorComposer constructor.
     * @param Department $department
     */
    function __construct(Department $department) {
        
        $this->department = $department;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $schoolId = $this->schoolId();
        $squads = Squad::whereIn('id', $this->classIds())
            ->where('enabled', 1)->pluck('name', 'id')
            ->toArray();
        $gradeIds = [];
        foreach ($squads as $id => $name) {
            $gradeIds[] = Squad::find($id)->grade_id;
        }
        $gradeIds = array_unique($gradeIds);
        $squads[0] = '(请选择)';
        ksort($squads);
        $subjects = Subject::whereSchoolId($schoolId)
            ->where('enabled', 1)->get();
        $subjectList = [];
        foreach ($subjects as $subject) {
            $intersect = array_intersect($gradeIds, explode(',', $subject->grade_ids));
            if (!empty($intersect)) {
                $subjectList[$subject->id] = $subject->name;
            }
        }
        $tags = Tag::whereSchoolId($schoolId)
            ->where('enabled', 1)->pluck('name', 'id')
            ->toArray();
        $groups = Group::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id')->toArray();
        $subjectList[0] = '(请选择)';
        ksort($subjectList);
        $mobiles = $selectedTags = $selectedDepartmentIds = $selectedDepartments = [];
        if (Request::route('id')) {
            $educator = Educator::find(Request::route('id'));
            $mobiles = $educator->user->mobiles;
            $selectedTags = $educator->user->tags->pluck('name', 'id')->toArray();
            $selectedDepartmentIds = $educator->user->departments->pluck('id')->toArray();
            $selectedDepartments = $this->selectedNodes($selectedDepartmentIds);
        }
        $view->with([
            'squads'                => $squads,
            'subjects'              => $subjectList,
            'groups'                => $groups,
            'mobiles'               => $mobiles,
            'tags'                  => $tags,
            'selectedTags'          => $selectedTags,
            'selectedDepartmentIds' => implode(',', $selectedDepartmentIds),
            'selectedDepartments'   => $selectedDepartments,
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
                'id'     => $department['id'],
                'parent' => $parentId,
                'text'   => $text,
                'icon'   => Constant::NODE_TYPES[$departmentType]['icon'],
                'type'   => Constant::NODE_TYPES[$departmentType]['type'],
            ];
        }
        
        return $nodes;
        
    }
    
}