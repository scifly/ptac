<?php
namespace App\Http\ViewComposers;

use App\Models\User;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class StudentComposer {

    protected $user;

    protected $class;

    public function __construct(User $user,Squad $class) {

        $this->user = $user;

        $this->class = $class;

    }

    public function compose(View $view) {

        $view->with([
            'user' => $this->user->pluck('realname','id'),

        ]);

        $view->with([
            'class' => $this->class->pluck('name', 'id'),

        ]);

    }

}