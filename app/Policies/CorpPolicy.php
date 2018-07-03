<?php

namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\Department;
use App\Models\Menu;
use App\Models\User;
use App\Models\Corp;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class CorpPolicy
 * @package App\Policies
 */
class CorpPolicy {

    use HandlesAuthorization, ModelTrait;
    
    protected $menu;
    
    /**
     * CorpPolicy constructor.
     * @param Menu $menu
     */
    function __construct(Menu $menu) {
        
        $this->menu = $menu;
        
    }
    
    /**
     * Determine whether the user can (v)iew/(e)dit/(u)pdate/(d)elete the corp.
     *
     * @param  User $user
     * @param  Corp $corp
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Corp $corp = null, $abort = false) {

        abort_if(
            $abort && !$corp,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->group->name;
        switch ($role) {
            case '运营':
                return true;
            case '企业':
                $paths = explode('/', Request::path());
                if (in_array($paths[1], ['index', 'create', 'store', 'delete'])) {
                    return false;
                }
                # userCorp - the Corp to which the user belongs
                $rootMenuId = $this->menu->rootMenuId();
                $userCorp = Corp::whereMenuId($rootMenuId)->first();
                if ($paths[1] == 'edit') {
                    return $userCorp->id == $corp->id;
                }
                $departmentId = Request::input('department_id');
                $department = Department::find($departmentId);
                if (!$department) { return false; }
                return ($userCorp->id == $corp->id)
                    && in_array($departmentId, $this->departmentIds($user->id))
                    && $department->departmentType->name == '企业';
            default:
                return false;
        }

    }

}
