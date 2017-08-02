<?php
namespace App\Http\ViewComposers;

use App\Models\Squad;
use App\Models\Subject;
use App\Models\Educator;
use Illuminate\Contracts\View\View;

class EducatorClassComposer {

    protected $squad;

    protected $subject;

    protected $educator;

    public function __construct(Squad $squad, Subject $subject, Educator $educator, Educator $educator) {
        $this->squad = $squad;
        $this->subject = $subject;
        $this->educator = $educator;

    }

    public function compose(View $view) {

        $educators = Educator::with('user')->get()->toArray();
        if(!empty($educators))
        {
            foreach ($educators  as $k=>$v)
            {
                $users[$v['id']] = $v['user']['realname'];
            }
        }
        $view->with([

            'users' => $users,

            'squad' => $this->squad->pluck('name', 'id'),

            'subject' => $this->subject->pluck('name', 'id'),

        ]);


    }

}