<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{ScoreRange, Subject, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use Request;

/**
 * Class ScoreRangePolicy
 * @package App\Policies
 */
class ScoreRangePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param ScoreRange|null $sr
     * @return bool
     */
    function operation(User $user, ScoreRange $sr = null) {
        
        $schoolId = $this->schoolId();
        $perm = true;
        !$sr ?: $perm &= $sr->school_id == $schoolId;
        !Request::has('subject_ids')
            ?: $perm &= Subject::whereSchoolId($schoolId)->pluck('id')->has(
            explode(',', Request::input('subject_ids'))
        );
        
        return $this->action($user) && $perm;
        
    }
    
}
