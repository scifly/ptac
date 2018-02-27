<?php
namespace App\Policies;

use App\Models\Corp;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class CommonPolicy {
    
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {  }
    
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
                School::schoolId(),
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
    
<<<<<<< HEAD:app/Policies/SchoolPolicy.php
        if (!$model) { abort(404); }
=======
        abort_if(
            !$model,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12:app/Policies/CommonPolicy.php
        $role = $user->group->name;
        if ($role == '运营') { return true; }
        $schoolId = $model->{'school_id'} ?? null;
        if ($role == '企业') {
            $corp = Corp::whereDepartmentId($user->topDeptId())->first();
            return in_array(
                $schoolId,
                $corp->schools->pluck('id')->toArray()
            );
        }
        
        return $schoolId == School::schoolId();
    
    }
    
}
