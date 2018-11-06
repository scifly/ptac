<?php
namespace App\Helpers;

use App\Models\Action;
use App\Models\ActionGroup;
use App\Models\User;
use Illuminate\Support\Facades\Request;

/**
 * Trait PolicyTrait
 * @package App\Helpers
 */
trait PolicyTrait {
    
    /**
     * @param User $user
     * @return bool
     */
    function action(User $user) {
    
        $actionId = Action::whereRoute(
            trim(Request::route()->uri())
        )->first()->id;
        $ag = ActionGroup::where([
            'group_id' => $user->group_id,
            'action_id' => $actionId
        ])->first();
        
        return $ag ? true : false;
        
    }

}

