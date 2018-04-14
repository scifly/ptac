<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy {
    
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    // public function create(User $user) {
    //
    //     return $this->permit($user);
    //
    // }
    //
    // public function store(User $user) {
    //
    //     return $this->permit($user);
    //
    // }
    //
    // public function edit(User $user, Company $company) {
    //
    //     return $this->permit($user, $company, true);
    //
    // }
    //
    // public function update(User $user, Company $company) {
    //
    //     return $this->permit($user, $company, true);
    //
    // }
    //
    // public function destroy(User $user, Company $company) {
    //
    //     return $this->permit($user, $company, true);
    //
    // }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param Company $company
     * @param bool $abort
     * @return bool
     */
    public function permit(User $user, Company $company = null, $abort = false) {
        
        abort_if(
            $abort && !$company,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}
