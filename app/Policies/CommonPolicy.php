<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class CommonPolicy
 * @package App\Policies
 */
class CommonPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    /**
     * 公用权限判断
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
        $role = $user->role();
        if ($role == '运营') return true;
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['index', 'create', 'store', 'issue'])) {
            return $isSuperRole ? true : $this->action($user);
        } elseif (in_array($action, ['show', 'edit', 'update', 'delete'])) {
            $accessible = in_array($model->{'school_id'}, $this->schoolIds());
            if (isset($model->{'user_id'}) && !$isSuperRole) {
                $accessible &= ($model->{'user_id'} == Auth::id());
            }
            
            return $isSuperRole ? $accessible : ($accessible && $this->action($user));
        }
        
        return false;
        
    }
    
}
