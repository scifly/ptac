<?php
namespace App\Http\ViewComposers;

use App\Models\Custodian;
use App\Models\User;
use App\Models\Squad;
use App\Models\Group;
use App\Models\Department;
use Illuminate\Contracts\View\View;

class StudentComposer {

    protected $user,$class,$custodian, $group;

    public function __construct(User $user,Squad $class, Group $group, Department $department, Custodian $custodian) {

        $this->user = $user;
        $this->class = $class;
        $this->group = $group;
        $this->custodian = $custodian;
        $this->department = $department;

    }

    public function compose(View $view) {
        $Custodian = Custodian::with('user')->get()->toArray();
        if(!empty($Custodian))
        {
            foreach ($Custodian as $k=>$v)
            {
                $custodian[$v['id']] = $v['user']['realname'];
            }
        }
        $view->with([
            'user' => $this->user->pluck('realname','id'),
            'class' => $this->class->pluck('name', 'id'),
            'groups' => $this->group->pluck('name', 'id'),
            'custodian' => $custodian,
            'departments' => $this->department->pluck('name','id'),
//            'departments' => $this->department->departments([1]),

        ]);

    }

}