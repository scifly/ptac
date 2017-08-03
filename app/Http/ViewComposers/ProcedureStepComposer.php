<?php

namespace App\Http\ViewComposers;

use App\Models\Educator;
use App\Models\User;
use App\Models\Procedure;
use Illuminate\Contracts\View\View;

class ProcedureStepComposer {

    protected $procedures;
    protected $educators;

    public function __construct(Procedure $procedures,User $educators) {
        $this->procedures = $procedures;
        $this->educators = $educators;
    }

    public function compose(View $view) {

        $data =  User::whereHas('educator')->get(['id','realname'])->toArray();

        $educators=array();

        if(!empty( $data ))
        {
            foreach ($data as $v){
                $educators[$v['id']] = $v['realname'];
            }
        }
        $view->with([
            'procedures' => $this->procedures->pluck('name', 'id'),
            'educators' => $this->educators->pluck('realname','id')
        ]);
    }

}