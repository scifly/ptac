<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class CommonPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    /**
     * (r)ead, (u)pdate, (d)elete
     *
     * @param User $user
     * @param Model $model
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Model $model = null, $abort = false) {
        
        abort_if(
            $abort && !$model,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $schoolId = $model->{'school_id'} ?? null;
        switch ($user->group->name) {
            case '运营':
            case '企业':
            case '学校':
                return in_array($schoolId, $this->schoolIds());
            default:
                return ($user->educator->school_id == $this->schoolId())
                    && $this->action($user);
        }
        
    }
    
}
