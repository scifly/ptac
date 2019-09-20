<?php
namespace App\Helpers;

use App\Models\{Action, ActionGroup, User};
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
    
        if (!in_array($user->role(), Constant::SUPER_ROLES)) {
            $actionId = Action::whereRoute(trim(Request::route()->uri()))->first()->id;
            $ag = ActionGroup::where(['group_id'  => $user->group_id, 'action_id' => $actionId])->first();
            $perm = $ag ? true : false;
        }
        
        return $perm ?? true;
        
    }
    
    /**
     * @param $field
     * @param $model
     * @return array|string|null
     */
    function field($field, $model) {
        
        return Request::input($field) ?? ($model ? $model->{$field} : null);
        
    }
    
}

