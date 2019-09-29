<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait, PolicyTrait};
use App\Models\{User, Wap, Column};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ColumnPolicy
 * @package App\Policies
 */
class ColumnPolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
    /**
     * @param User $user
     * @param Column $column
     * @return bool
     */
    function operation(User $user, Column $column = null) {
        
        $perm = true;
        [$wapId, $ids] = array_map(
            function ($field) use ($column) {
                return $this->field($field, $column);
            }, ['wap_id', 'ids']
        );
        $schoolIds = $this->schoolIds()->flip();
        !$wapId ?: $perm &= $schoolIds->has(Wap::find($wapId)->school_id);
        !$ids ?: $perm &= Wap::whereSchoolId($this->schoolId())->first()
                ->modules->pluck('id')->flip()->has(array_values($ids));
        
        return in_array($user->role(), Constant::SUPER_ROLES) && $perm;
        
    }
    
}
