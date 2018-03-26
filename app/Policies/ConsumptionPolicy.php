<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Models\ActionGroup;
use App\Models\Grade;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsumptionPolicy {
    
    use HandlesAuthorization, ModelTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    public function show(User $user) {
        
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
        
        return ActionGroup::whereGroupId($user->group_id)->first() ? true : false;
        
    }
    
    public function stat(User $user, ConsumptionStat $cs) {
        
        switch ($cs->rangeId) {
            case 1:
                $studentIds = [$cs->studentId];
                break;
            case 2:
                $studentIds = Squad::find($cs->classId)
                    ->students->pluck('id')->toArray();
                break;
            case 3:
                $studentIds = Grade::find($cs->gradeId)
                    ->students->pluck('id')->toArray();
                break;
            default:
                return false;
            
        }
        $dateRange = explode(' - ', $cs->dateRange);
        
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return empty(array_diff($studentIds, $this->contactIds('student')))
                && ($dateRange[1] >= $dateRange[0]);
        }
        
        return empty(array_diff($studentIds, $this->contactIds('student')))
            && ($dateRange[1] >= $dateRange[0])
            && (ActionGroup::whereGroupId($user->group_id)->first() ? true : false);
        
    }
    
    public function export(User $user) {
    
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
    
        return ActionGroup::whereGroupId($user->group_id)->first() ? true : false;
        
    }
    
}
