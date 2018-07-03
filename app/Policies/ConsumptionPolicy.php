<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Action;
use App\Models\ActionGroup;
use App\Models\Grade;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * Class ConsumptionPolicy
 * @package App\Policies
 */
class ConsumptionPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * @param User $user
     * @return bool
     */
    public function show(User $user) {
        
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
        $actionId = Action::whereRoute(trim(Request::route()->uri()))->first()->id;
        $ag = ActionGroup::whereGroupId($user->group_id)->where('action_id', $actionId)->first();
    
        return $ag ? true : false;
        
    }
    
    /**
     * @param User $user
     * @param ConsumptionStat $cs
     * @return bool
     */
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
            Log::debug('student_ids: ' . json_encode($studentIds));
            Log::debug('studentIds: ' . json_encode($this->contactIds('student')));
            return empty(array_diff($studentIds, $this->contactIds('student')))
                && ($dateRange[1] >= $dateRange[0]);
        }
    
        return empty(array_diff($studentIds, $this->contactIds('student')))
            && ($dateRange[1] >= $dateRange[0])
            && $this->action($user);
        
    }
    
    /**
     * @param User $user
     * @return bool
     */
    function export(User $user) {
    
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
        
        return $this->action($user);
        
    }
    
}
