<?php

namespace App\Policies;

use App\Models\Action;
use App\Models\ActionGroup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class ActionPolicy {
    use HandlesAuthorization;

    protected $roles = ['运营', '企业', '学校'];
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //

    }

    public function act(User $user, Route $route) {

        $role = $user->group->name;
        if (!in_array($role, $this->roles)) {
            $actionGroup = ActionGroup::whereGroupId($user->group_id)
                ->where('action_id', Action::whereRoute($route->uri)->first()->id)
                ->first();
            return $actionGroup ? true : false;
        }
        return true;

    }

}
