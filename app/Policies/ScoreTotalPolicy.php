<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\ScoreTotal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class ScoreTotalPolicy
 * @package App\Policies
 */
class ScoreTotalPolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param ScoreTotal|null $st
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, ScoreTotal $st = null, $abort = false) {
        
        abort_if(
            $abort && !$st,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if ($user->group->name == '运营') { return true; }
        $isSuperRole = in_array($user->group->name, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
            case 'stat':
                return $isSuperRole ? true : $this->action($user);
            default:
                return false;
        }
    }
    
}
