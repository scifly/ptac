<?php
namespace App\Http\ViewComposers;

use App\Models\Department;
use App\Models\Corp;
use App\Models\School;
use Illuminate\Contracts\View\View;

class DepartmentComposer {

    protected $department;
    protected $corp;
    protected $school;

    public function __construct(Department $department , Corp $corp, School $school) {

        $this->department = $department;
        $this->corp = $corp;
        $this->school = $school;

    }

    public function compose(View $view) {
        $parents[] = '无';
        $parents_id = $this->department->where('parent_id','=',0)->pluck('name', 'id');
        foreach ($parents_id as $key=>$value)
        {
            $parents[$key]=$value;
        }
        $view->with([
            'parents' => $parents,
            'corps' => $this->corp->pluck('name', 'id'),
            'schools' => $this->school->pluck('name', 'id'),
        ]);

    }

}