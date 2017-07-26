<?php

namespace App\Http\ViewComposers;

use App\Models\Procedure;
use App\Models\User;
use Illuminate\Contracts\View\View;

class ProcedureStepComposer {

    protected $procedures;
    protected $approver_users;
    protected $related_users;

    public function __construct(Procedure $procedures, User $approver_users, User $related_users) {
        $this->procedures = $procedures;
        $this->approver_users = $approver_users;
        $this->related_users = $related_users;
    }

    public function compose(View $view) {

        $view->with([
            'procedures' => $this->procedures->pluck('name', 'id'),
            'approver_users' => $this->approver_users->pluck('realname', 'id'),
            'related_users' => $this->related_users->pluck('realname', 'id'),
        ]);
    }

}