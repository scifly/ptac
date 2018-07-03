<?php
namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class OrderPolicy
 * @package App\Policies
 */
class OrderPolicy {
    
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
}
