<?php
namespace App\Http\ViewComposers;

use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\View;

class CustodianComposer {
    
    protected $group, $user, $student;
    
    public function __construct(Group $group, User $user, Student $student) {
        
        $this->group = $group;
        $this->user = $user;
        $this->student = $student;
        
    }
    
    public function compose(View $view) {
        $Students = Student::with('user')->get()->toArray();
        if (!empty($Students)) {
            foreach ($Students as $k => $v) {
                $students[$v['id']] = $v['user']['realname'];
            }
        }
        $view->with([
            'user'     => $this->user->pluck('realname', 'id'),
//            'departments' => $this->department->departments([1]),
            'groups'   => $this->group->pluck('name', 'id'),
            'students' => $students,
        ]);
        
    }
    
}