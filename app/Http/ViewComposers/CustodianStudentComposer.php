<?php
namespace App\Http\ViewComposers;

use App\Models\Custodian;
use App\Models\User;
use App\Models\Student;
use Illuminate\Contracts\View\View;

class CustodianStudentComposer {

    protected $custodian;

    protected $user;

    public function __construct(Custodian $custodian, User $user, Student $student) {

        $this->custodian = $custodian;
        $this->user = $user;
        $this->student = $student;

    }

    public function compose(View $view) {

        $custodians =Custodian::with('user')->get()->toArray();
        if(!empty($custodians))
        {
            foreach ($custodians as $k=>$v)
            {
                $cus_name[$v['id']] = $v['user']['realname'];
            }
        }
        $students = Student::with('user')->get()->toArray();
        if(!empty($students))
        {
            foreach ($students as $k=>$v)
            {
                $student_name[$v['id']] = $v['user']['realname'];
            }
        }

        $view->with([
            'student_name' => $student_name,
            'cus_name' => $cus_name]
        );



    }

}