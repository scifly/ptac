<?php

namespace App\Http\ViewComposers;

use App\Models\Corp;
use App\Models\Educator;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class GradeComposer {

    protected $schools;
    protected $educators;

    public function __construct(School $schools, Educator $educators) {

        $this->schools = $schools;
        $this->educators = $educators;

    }

    public function compose(View $view) {


        $data =  User::whereHas('educator')->get(['id','username'])->toArray();
        $educators=array();
        if(!empty( $data ))
        {
            foreach ($data as $v){
                $educators[$v['id']] = $v['username'];
            }
        }

        $view->with([
            'schools' => $this->schools->pluck('name', 'id'),
            'educators' => $educators,
        ]);
    }

}