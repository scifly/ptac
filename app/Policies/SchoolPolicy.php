<?php
namespace App\Policies;

use App\Models\Corp;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class SchoolPolicy {
    
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
        
        $role = $user->group->name;
        if ($role == '运营') { return true; }
        if ($role == '企业') {
            $corp = Corp::whereDepartmentId($user->topDeptId())->first();
            return in_array(School::schoolId(), $corp->schools->pluck('id')->toArray());
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
    
        if (!$model) { abort(404); }
        $role = $user->group->name;
        if ($role == '运营') { return true; }
        $schoolId = $model->{'school_id'} ?? null;
        if ($role == '企业') {
            $corp = Corp::whereDepartmentId($user->topDeptId())->first();
            return in_array($schoolId, $corp->schools->pluck('id')->toArray());
        }
        
        return $schoolId == School::schoolId();
    
    }
    
}
