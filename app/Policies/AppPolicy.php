<?php

namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\App;
use App\Models\Corp;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class AppPolicy
 * @package App\Policies
 */
class AppPolicy {

    use HandlesAuthorization;
    
    protected $menu;
    
    /**
     * Create a new policy instance.
     *
     * @param Menu $menu
     */
    public function __construct(Menu $menu) {
        
        $this->menu = $menu;
        
    }
    
    /**
     * Determine whether the user can (e)dit/(u)pdate/sync (m)enu of the app
     *
     * @param User $user
     * @param App $app
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, App $app = null, $abort = false) {

        abort_if(
            $abort && !$app,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->group->name;
        switch ($role) {
            case '运营':
                return true;
            case '企业':
                $rootMenuId = $this->menu->rootMenuId();
                $corp = Corp::whereMenuId($rootMenuId)->first();
                return $corp->id == $app->corp_id;
            default:
                return false;
        }

    }

}
