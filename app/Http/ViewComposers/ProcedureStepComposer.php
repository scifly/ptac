<?php

namespace App\Http\ViewComposers;

use App\Models\Educator;
use App\Models\Procedure;
use Illuminate\Contracts\View\View;

class ProcedureStepComposer {

    protected $procedures;
    protected $educators;

    public function __construct(Procedure $procedures,Educator $educators) {
        $this->procedures = $procedures;
        $this->educators = $educators;
    }

    public function compose(View $view) {

        $data =  Educator::with('user')->get()->toArray();

        $educators=array();

        if(!empty( $data ))
        {
            foreach ($data as $v){
                $educators[$v['id']] = $v['user']['realname'];
            }
        }
        $view->with([
            'procedures' => $this->procedures->pluck('name', 'id'),
            'educators' => $educators
        ]);
    }

}