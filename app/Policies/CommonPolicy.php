<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class CommonPolicy {
    
    use HandlesAuthorization, ModelTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    /**
     * (c)reate
     *
     * @param User $user
     * @return bool
     */
    public function c(User $user) {
        
        if ($user->group->name == '企业') {
            $corp = Corp::whereDepartmentId($user->topDeptId())->first();
            
            return in_array(
                $this->schoolId(),
                $corp->schools->pluck('id')->toArray()
            );
        }
        
        return true;
        
    }
    
    /**
     * (r)ead, (u)pdate, (d)elete
     *
     * @param User $user
     * @param Model $model
     * @return bool
     */
    public function rud(User $user, Model $model) {
        
        abort_if(
            !$model,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->group->name;
        if ($role == '运营') {
            return true;
        }
        $schoolId = $model->{'school_id'} ?? null;
        if ($role == '企业') {
            $corp = Corp::whereDepartmentId($user->topDeptId())->first();
            
            return in_array(
                $schoolId,
                $corp->schools->pluck('id')->toArray()
            );
        }
        
        return $schoolId == $this->schoolId();
        
    }
    
}
