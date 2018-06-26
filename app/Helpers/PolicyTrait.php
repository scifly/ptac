<?php
namespace App\Helpers;

use App\Models\Action;
use App\Models\ActionGroup;
use App\Models\User;
use Illuminate\Support\Facades\Request;

trait PolicyTrait {

    function action(User $user) {
    
        $actionId = Action::whereRoute(trim(Request::route()->uri()))->first()->id;
        $ag = ActionGroup::whereGroupId($user->group_id)->where('action_id', $actionId)->first();
        
        return $ag ? true : false;
        
    }

}

