<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class CompanyPolicy
 * @package App\Policies
 */
class CompanyPolicy {
    
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param Company $company
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Company $company = null, $abort = false) {
        
        abort_if(
            $abort && !$company,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->role() == '运营';
        
    }
    
}