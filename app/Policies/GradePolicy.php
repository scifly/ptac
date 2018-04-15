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
use Illuminate\Support\Facades\Request;

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
    
    public function create(User $user) {
    
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
        return $this->action($user);
        
    }
    
    public function store(User $user) {
        
        $schoolId = Request::input('school_id');
        $departmentId = Request::input('department_id');
        $educatorIds = Request::input('educator_ids');
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return in_array($schoolId, $this->schoolIds())
                && in_array($departmentId, $this->departmentIds($user->id))
                && empty(array_diff($educatorIds, $this->contactIds('educator')));
        }
        
        return in_array($schoolId, $this->schoolIds())
            && in_array($departmentId, $this->departmentIds($user->id))
            && empty(array_diff($educatorIds, $this->contactIds('educator')))
            && $this->action($user);
        
    }
    
    public function edit(User $user, Grade $grade) {
        
        return $this->objectPerm($user, $grade);
        
    }
    
    public function update(User $user, Grade $grade) {
    
        abort_if(
            !$grade,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $schoolId = Request::input('school_id');
        $departmentId = Request::input('department_id');
        $educatorIds = Request::input('educator_ids');
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return in_array($schoolId, $this->schoolIds())
                && in_array($departmentId, $this->departmentIds($user->id))
                && empty(array_diff($educatorIds, $this->contactIds('educator')));
        }
    
        return in_array($schoolId, $this->schoolIds())
            && in_array($departmentId, $this->departmentIds($user->id))
            && empty(array_diff($educatorIds, $this->contactIds('educator')))
            && $this->action($user);
        
    }
    
    public function destroy(User $user, Grade $grade) {
        
        return $this->objectPerm($user, $grade);
        
    }
    
    private function objectPerm(User $user, Grade $grade) {
    
        abort_if(
            !$grade,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        switch ($user->group->name) {
            case '运营': return true;
            case '企业':
            case '学校':
                return in_array($grade->id, $this->gradeIds());
            default:
                return $this->action($user) && (in_array($grade->id, $this->gradeIds()));
        }
    
    }
    
}
