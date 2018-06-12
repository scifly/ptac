<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class SquadPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    public function cs(User $user) {
        
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
        
        return $this->action($user);
        
    }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param Squad|null $class
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, Squad $class = null, $abort = false) {
        
        abort_if(
            $abort && !$class,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if ($user->group->name == '运营') { return true; }
        $isClassAllowed = $isGradeAllowed = $isEducatorAllowed = false;
        $isSuperRole = in_array($user->group->name, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['store', 'update'])) {
            $gradeId = Request::input('grade_id');
            $educatorIds = explode(',', Request::input('educator_ids'));
            $isGradeAllowed = in_array($gradeId, $this->gradeIds());
            $isEducatorAllowed = empty(array_diff($educatorIds, $this->contactIds('educator')));
        }
        if (in_array($action, ['edit', 'update', 'delete'])) {
            $isClassAllowed = in_array($class->id, $this->classIds());
        }
        switch ($action) {
            case 'index':
            case 'create':
                return $isSuperRole ? true : $this->action($user);
            case 'edit':
            case 'delete':
                return $isSuperRole ? $isClassAllowed : ($isClassAllowed && $this->action($user));
            case 'update':
                Log::debug($isClassAllowed);
                Log::debug($isGradeAllowed);
                Log::debug($isEducatorAllowed);
                return $isSuperRole
                    ? ($isClassAllowed && $isGradeAllowed && $isEducatorAllowed)
                    : ($isClassAllowed && $isGradeAllowed && $isEducatorAllowed && $this->action($user));
            default:
                return false;
        }
        
    }
    
}
