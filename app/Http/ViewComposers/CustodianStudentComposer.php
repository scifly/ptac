<?php
namespace App\Http\ViewComposers;

use App\Models\Custodian;
use App\Models\User;
use Illuminate\Contracts\View\View;

class CustodianStudentComposer {

    protected $custodian;

    protected $user;

    public function __construct(Custodian $custodian, User $user) {

        $this->custodian = $custodian;
        $this->user = $user;

    }

    public function compose(View $view) {

        $custodian = $this->custodian->pluck('user_id', 'id')->toArray();

        $cus_name = [];

        foreach ($custodian as $k=>$v)
        {
            $user = $this->user->where('id',$v)->pluck('realname');

            $cus_name[$k] = $user[0];

        }

        $view->with(['cus_name' => $cus_name ]);

//        $view->with([
//
//            'squad' => $this->squad->pluck('name', 'id'),
//
//        ]);


    }

}