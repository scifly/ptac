<?php
namespace App\Http\ViewComposers;

use App\Models\Squad;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Contracts\View\View;

class EducatorClassComposer {

    protected $squad;

    protected $subject;

    protected $user;

    public function __construct(Squad $squad, Subject $subject, User $user) {

        $this->user = $user;
        $this->squad = $squad;
        $this->subject = $subject;

    }

    public function compose(View $view) {


        $view->with([

            'users' => $this->user->pluck('realname', 'id'),

        ]);

        $view->with([

            'squad' => $this->squad->pluck('name', 'id'),

        ]);

        $view->with([

            'subject' => $this->subject->pluck('name', 'id'),

        ]);
    }

}