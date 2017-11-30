<?php
namespace App\Http\ViewComposers;

use App\Models\Educator;
use App\Models\School;
use Illuminate\Contracts\View\View;

class GradeComposer {

    protected $school;

    public function __construct(School $school) {
        $this->school = $school;
    }

    public function compose(View $view) {
        
        $schoolId = $this->school->getSchoolId();
        $educators = Educator::whereSchoolId($schoolId)
            ->where('enabled',1)
            ->get();
        $educatorUsers = [];
        foreach ($educators as $educator) {
            $educatorUsers[$educator->id] = $educator->user->realname;
        }
        $view->with([
            'schoolId'  => $schoolId,
            'educators' => $educatorUsers,
        ]);
    }

}