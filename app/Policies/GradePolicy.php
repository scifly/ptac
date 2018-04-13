<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\ActionGroup;
use App\Models\Corp;
use App\Models\Grade;
use App\Models\Menu;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
        
        return $this->classPerm($user);
        
    }
    
    public function store(User $user) {
        
        return $this->classPerm($user);
        
    }
    
    private function classPerm(User $user) {
        
        $role = $user->group->name;
        $rootMenuId = $this->menu->rootMenuId();
        switch ($role) {
            case '运营':
                return true;
            case '企业':
                $corp = Corp::whereMenuId($rootMenuId)->first();
                return in_array(
                    $this->schoolId(),
                    $corp->schools->pluck('id')->toArray()
                );
            case '学校':
                $school = School::whereMenuId($rootMenuId)->first();
                return $this->schoolId() == $school->id;
            default:
                return ($user->educator->school_id == $this->schoolId()) && $this->action($user);
        }
        
        
    }
    
    public function edit(User $user, Grade $grade) {
        
        return $this->objectPerm($user, $grade);
        
    }
    
    public function update(User $user, Grade $grade) {
        
        return $this->objectPerm($user, $grade);
        
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
        $role = $user->group->name;
        $rootMenuId = $this->menu->rootMenuId();
        switch ($role) {
            case '运营': return true;
            case '企业':
                $corp = Corp::whereMenuId($rootMenuId)->first();
                return in_array(
                    $grade->school_id,
                    $corp->schools->pluck('id')->toArray()
                );
            default:
                return ($user->educator->school_id == $grade->school_id)
                    && $this->action($user)
                    && (in_array($grade->id, $this->gradeIds()));
        }
    
    }
    
}
