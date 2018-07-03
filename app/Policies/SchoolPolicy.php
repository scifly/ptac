<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class SchoolPolicy
 * @package App\Policies
 */
class SchoolPolicy {
    
    use HandlesAuthorization, ModelTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param School $school
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, School $school = null, $abort = false) {
        
        abort_if(
            $abort && !$school,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if ($user->group->name == '运营') { return true; }
        $isSchoolAllowed = false;
        $isSuperRole = in_array($user->group->name, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['index', 'create', 'store', 'edit', 'update', 'delete'])) {
            $isSchoolAllowed = in_array($school->id, $this->schoolIds());
        }
        switch ($action) {
            case 'index':
            case 'create':
            case 'store':
                return $user->group->name == '企业';
            case 'show':
            case 'edit':
            case 'update':
                return $isSuperRole && $isSchoolAllowed;
            case 'delete':
                return $user->group->name == '企业' && $isSchoolAllowed;
            default:
                return false;
        }
        
    }
    
}