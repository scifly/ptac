<?php

namespace App\Http\ViewComposers;

use App\Models\Corp;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class SquadComposer {

    protected $grades;
    protected $educators;

    public function __construct(Grade $grades, Educator $educators) {

        $this->grades = $grades;
        $this->educators = $educators;

    }

    public function compose(View $view) {


        $data = Educator::with('user')->get()->toArray();
        $educators=array();
        if(!empty( $data ))
        {
            foreach ($data as $v){
                $educators[$v['id']] = $v['user']['username'];
            }
        }

        $view->with([
            'grades' => $this->grades->pluck('name', 'id'),
            'educators' => $educators,
        ]);
    }

}