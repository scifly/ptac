<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{ScoreRange, Subject, User};
use Illuminate\Auth\Access\HandlesAuthorization;

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
        
        [$schoolId, $subjectIds] = array_map(
            function ($field) use ($sr) {
                return $this->field($field, $sr);
            }, ['school_id', 'subject_ids']
        );
        if (isset($schoolId, $subjectIds)) {
            $perm = $schoolId == $this->schoolId()
                && Subject::whereSchoolId($this->schoolId())->pluck('id')
                    ->has(explode(',', $subjectIds));
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}
