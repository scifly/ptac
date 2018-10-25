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
        if ($role == '运营') { return true; }
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
            case 'create':
            case 'store':
                return $isSuperRole ? true : $this->action($user);
            case 'show':
            case 'edit':
            case 'update':
            case 'delete':
                $isModelAllowed = in_array($model->{'school_id'}, $this->schoolIds());
                if (isset($model->{'user_id'}) && !$isSuperRole) {
                    $isModelAllowed = $isModelAllowed && ($model->{'user_id'} == Auth::id());
                }
                return $isSuperRole ? $isModelAllowed : ($isModelAllowed && $this->action($user));
            default:
                return false;
        }
        
    }
    
}
