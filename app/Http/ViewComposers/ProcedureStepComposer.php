<?php

namespace App\Http\ViewComposers;

use App\Models\User;
use App\Models\Procedure;
use Illuminate\Contracts\View\View;

class ProcedureStepComposer {

    protected $procedures;
    protected $educators;

    public function __construct(Procedure $procedures,User $users) {
        $this->procedures = $procedures;
        $this->users = $users;
    }

    public function compose(View $view) {

        $data =  User::whereHas('educators')->get();

        $users=array();

        if(!empty( $data ))
        {
            foreach ($data as $v){
                $users[$v['id']] = $v['realname'];
            }
        }
        $view->with([
            'procedures' => $this->procedures->pluck('name', 'id'),
            'users' => $users
        ]);
    }

}