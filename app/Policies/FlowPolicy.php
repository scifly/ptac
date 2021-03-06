<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Flow, FlowType, User};
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class FlowPolicy
 * @package App\Policies
 */
class FlowPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Flow|null $flow
     * @return bool
     * @throws Exception
     */
    public function operation(User $user, Flow $flow = null) {
    
        $perm = true;
        [$flowTypeId, $userId, $step, $ids] = array_map(
            function ($field) use ($flow) {
                return Request::input($field, $flow);
            }, ['flow_type_id', 'user_id', 'step', 'ids']
        );
        if (isset($flowTypeId, $userId, $step)) {
            $flowType = FlowType::find($flowTypeId);
            $steps = json_decode($flowType, true);
            $perm &= collect(explode(',', $this->visibleUserIds()))->flip()->has($userId)
                && $flowType->school_id == $this->schoolId()
                && ($user->id == $userId ? $step == 0 : in_array($user->id, $steps[$step]['userIds']));
        }
        !$ids ?: $perm &= Flow::where('user_id', Auth::id())
            ->pluck('id')->flip()->has(array_values($ids));
        
        return $this->action($user) && $perm;
    
    }
    
}