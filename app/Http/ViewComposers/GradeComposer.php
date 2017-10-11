<?php
namespace App\Http\ViewComposers;

use App\Models\Educator;
use App\Models\School;
use Illuminate\Contracts\View\View;

class GradeComposer {

    protected $school;
    protected $educator;

    public function __construct(School $school, Educator $educator) {

        $this->school = $school;
        $this->educator = $educator;

    }

    public function compose(View $view) {

        $educators = $this->educator->all();
        $educatorUsers = [];
        foreach ($educators as $educator) {
            $educatorUsers[$educator->id] = $educator->user->realname;
        }
        $view->with([
            'schools'   => $this->school->pluck('name', 'id'),
            'educators' => $educatorUsers,
        ]);
    }

}