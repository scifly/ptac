<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Corp;
use App\Models\School;
use App\Models\User;
use App\Models\WapSite;
use Illuminate\Auth\Access\HandlesAuthorization;

class WapSitePolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    public function edit(User $user, WapSite $ws) {
        
        return $this->permit($user, $ws);
        
    }
    
    public function update(User $user, WapSite $ws) {
        
        return $this->permit($user, $ws);
        
    }
    
    private function permit(User $user, WapSite $ws) {
        
        abort_if(
            !$ws,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        switch ($user->group->name) {
            case '运营':
                return true;
            case '企业':
                $departmentId = $this->head($user);
                $corp = Corp::whereDepartmentId($departmentId)->first();
                $schooldIds = School::whereCorpId($corp->id)->pluck('id')->toArray();
                return in_array($ws->school_id, $schooldIds);
            case '学校':
                $departmentId = $this->head($user);
                $school = School::whereDepartmentId($departmentId)->first();
                return $ws->school_id == $school->id;
            default:
                $school = $user->educator->school;
                return $ws->school_id == $school->id && $this->action($user);
        }
        
    }
    
}
