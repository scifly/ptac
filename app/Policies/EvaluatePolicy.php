<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Educator, Evaluate, Indicator, Semester, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use ReflectionException;

/**
 * Class EvaluatePolicy
 * @package App\Policies
 */
class EvaluatePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Evaluate $eval
     * @return bool
     * @throws ReflectionException
     */
    public function operation(User $user, Evaluate $eval = null) {

        if (!$user->educator) return false;
        [$studentId, $indicatorId, $semesterId, $eduatorId] = array_map(
            function ($field) use ($eval) {
                return $this->field($field, $eval);
            }, ['student_id', 'indicator_id', 'semester_id', 'educator_id']
        );
        $schoolId = $this->schoolId();
        $perm = Auth::user()->educator->school_id == $schoolId;;
        if (isset($studentId, $indicatorId, $semesterId, $eduatorId)) {
            $perm &= collect($this->contactIds('student'))->flip()->has($studentId)
                && Indicator::whereSchoolId($schoolId)->pluck('id')->flip()->has($indicatorId)
                && Semester::whereSchoolId($schoolId)->pluck('id')->flip()->has($semesterId)
                && Educator::find($eduatorId)->user_id == Auth::id();
        }
        
        return $this->action($user) && $perm;
        
    }
    
}
