<?php
namespace App\Http\ViewComposers;

use App\Models\Squad;
use App\Models\Subject;
use App\Models\Educator;
use App\Models\User;
use Illuminate\Contracts\View\View;

class EducatorClassComposer {

    protected $squad;

    protected $subject;

    protected $educator;

    protected $user;

    public function __construct(Squad $squad, Subject $subject, Educator $educator, User $user) {

        $this->educator = $educator;
        $this->user = $user;
        $this->squad = $squad;
        $this->subject = $subject;

    }

    public function compose(View $view) {

        $educator = $this->educator->pluck('user_id', 'id')->toArray();

        $users = [];

        foreach ($educator as $k=>$v)
        {
            $user = $this->user->where('id',$v)->pluck('realname');

            $users[$k] = $user[0];

        }

        $view->with(['users' => $users ]);

        $view->with([

            'squad' => $this->squad->pluck('name', 'id'),

        ]);

        $view->with([

            'subject' => $this->subject->pluck('name', 'id'),

        ]);
    }

}