<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\ActionGroup;
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
        
        $role = $user->group->name;
        switch ($role) {
            case '运营':
                return true;
            case '企业':
            case '学校':
                return in_array($this->schoolId(), $this->schoolIds());
            default:
                return in_array($this->schoolId(), $this->schoolIds())
                    && (ActionGroup::whereGroupId($user->group_id)->first() ? true : false);
        }
        
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
        $schoolId = $model->{'school_id'} ?? null;
        switch ($role) {
            case '运营':
            case '企业':
            case '学校':
                return in_array($schoolId, $this->schoolIds());
            default:
                return ($user->educator->school_id == $this->schoolId())
                    && (ActionGroup::whereGroupId($user->group_id)->first() ? true : false);
        }
        
    }
    
}
