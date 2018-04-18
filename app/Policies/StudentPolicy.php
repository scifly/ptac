<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class StudentPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    /**
     * (c)reate, (s)tore, (i)mport, (e)xport
     *
     * @param User $user
     * @return bool
     */
    public function csie(User $user) {
    
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
        
        return $this->action($user);
    
    }
    
    /**
     * (s)how, (e)dit, (u)pdate, (d)estroy
     *
     * @param User $user
     * @param Student $student
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, Student $student = null, $abort = false) {
        
        abort_if(
            $abort && !$student,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if ($user->group->name == '运营') { return true; }
        $isSuperRole = in_array($user->group->name, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['export'])) {
        
        }
        
        
    }
    
}
