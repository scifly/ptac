<?php

namespace App\Http\ViewComposers;

use App\Models\Group;
use App\Models\User;
use App\Models\Student;
use App\Models\Department;
use Illuminate\Contracts\View\View;

class CustodianComposer {

    protected $group,$user,$student, $department;

    
    public function __construct(Group $group,User $user,Student $student, Department $department) {
        
        $this->group = $group;
        $this->user = $user;
        $this->student = $student;
        $this->department = $department;
        
    }
    
    public function compose(View $view)
    {
        $Students = Student::with('user')->get()->toArray();
        if(!empty($Students))
        {
            foreach ($Students as $k=>$v)
            {
                $students[$v['id']] = $v['user']['realname'];
            }
        }

        $view->with([
            'departments' => $this->department->pluck('name','id'),
//            'departments' => $this->department->departments([1]),
            'groups' => $this->group->pluck('name', 'id'),
            'students' => $students
        ]);

    }
    
}