<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\School;
use Illuminate\Contracts\View\View;

class SquadComposer {
    use ControllerTrait;
    protected $grades;
    protected $educators;
    protected $school;

    public function __construct(Grade $grades, Educator $educators, School $school) {

        $this->grades = $grades;
        $this->educators = $educators;
        $this->school = $school;

    }

    public function compose(View $view) {
        $schoolId = $this->school->getSchoolId();
        $grades = Grade::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        $data = Educator::with('user')
            ->where('school_id', $schoolId)
            ->get()->toArray();
        $educators = [];
        if (!empty($data)) {
            foreach ($data as $v) {
                $educators[$v['id']] = $v['user']['realname'];
            }
        }
        $view->with([
            'grades' => $grades,
            'educators' => $educators,
            'uris' => $this->uris()

        ]);
    }

}