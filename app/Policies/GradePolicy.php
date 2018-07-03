<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Grade;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * Class GradePolicy
 * @package App\Policies
 */
class GradePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
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
     * 权限判断
     *
     * @param User $user
     * @param Grade|null $grade
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Grade $grade = null, $abort = false) {
    
        abort_if(
            $abort && !$grade,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if ($user->group->name == '运营') { return true; }
        $action = explode('/', Request::path())[1];
        $isSuperRole = in_array($user->group->name, Constant::SUPER_ROLES);
        $isGradeAllowed = $isDepartmentAllowed = $isEducatorAllowed = false;
        if (in_array($action, ['store', 'update'])) {
            $departmentId = Request::input('department_id');
            Log::debug($departmentId);
            $educatorIds = Request::input('educator_ids') ?? [];
            $isDepartmentAllowed = !$departmentId ? true : in_array($departmentId, $this->departmentIds($user->id));
            $isEducatorAllowed = empty(array_diff($educatorIds, $this->contactIds('educator')));
        }
        if (in_array($action, ['edit', 'update', 'destroy'])) {
            $isGradeAllowed = in_array($grade->id, $this->gradeIds());
        }
        switch ($action) {
            case 'index':
            case 'create':
                return $isSuperRole ? true : $this->action($user);
            case 'store':
                return $isSuperRole
                    ? ($isEducatorAllowed && $isDepartmentAllowed)
                    : ($isEducatorAllowed && $isDepartmentAllowed && $this->action($user));
            case 'edit':
            case 'destroy':
                return $isSuperRole ? $isGradeAllowed : ($isGradeAllowed && $this->action($user));
            case 'update':
                return $isSuperRole
                    ? ($isGradeAllowed && $isEducatorAllowed && $isDepartmentAllowed)
                    : ($isGradeAllowed && $isEducatorAllowed && $isDepartmentAllowed && $this->action($user));
            default:
                return false;
        }
    
    }
    
}