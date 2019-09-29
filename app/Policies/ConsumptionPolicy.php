<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Consumption, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;
use ReflectionException;

/**
 * Class ConsumptionPolicy
 * @package App\Policies
 */
class ConsumptionPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @return bool
     * @throws ReflectionException
     */
    function operation(User $user) {

        if ($rangeId = Request::input('range_id')) {
            if (!in_array($rangeId, range(1, 3))) return false;
            $ids = (new Consumption)->studentIds($rangeId);
            $dRange = explode(' - ', Request::input('date_range'));
            $perm = $this->contactIds('student')->flip()->has(array_values($ids))
                && ($dRange[1] >= $dRange[0]);
        }
        
        return $this->action($user) && ($perm ?? true);
    
    }
    
}
