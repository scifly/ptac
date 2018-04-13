<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Models\Corp;
use App\Models\Group;
use App\Models\Menu;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy {
    
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
    
    public function create(User $user) {
        
        return $this->classPerm($user);
        
    }
    
    public function store(User $user) {
        
        return $this->classPerm($user);
        
    }
    
    private function classPerm(User $user) {
    
        return in_array(
            $user->group->name,
            Constant::SUPER_ROLES
        );
    
    }
    
    public function edit(User $user, Group $group) {
        
        return $this->objectPerm($user, $group);
        
    }
    
    public function update(User $user, Group $group) {
        
        return $this->objectPerm($user, $group);
        
    }
    
    public function destroy(User $user, Group $group) {
        
        return $this->objectPerm($user, $group);
        
    }
    
    private function objectPerm(User $user, Group $group) {
        
        abort_if(
            !$group,
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
                    $group->school->id,
                    $corp->schools->pluck('id')->toArray()
                );
            case '学校':
                $school = School::whereMenuId($rootMenuId)->first();
                return $school->id == $group->school->id;
            default: return false;
        }
        
    }
    
}
