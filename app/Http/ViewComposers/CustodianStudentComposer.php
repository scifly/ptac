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

        $custodian = $this->custodian->pluck('user_id','id')->toArray();

        //去除数组冲重复的user_id值
        $custodian = array_unique($custodian);
        foreach ($custodian as $k=>$v)
        {
            $user = $this->user->where('id',$v)->pluck('realname');

            //$k为cunstodians表中的id
            $cus_name[$k] = $user[0];

        }
        $view->with([ 'cus_name' => $cus_name ]);

        $student = $this->student->pluck('user_id','id')->toArray();

        $student = array_unique($student);

        foreach ($student as $k=>$v)
        {
            $user = $this->user->where('id',$v)->pluck('realname');
            //$k为students表中的id
            $student_name[$k] = $user[0];

        }

        $view->with([ 'student_name' => $student_name ]);



    }

}