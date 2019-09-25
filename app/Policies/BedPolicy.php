<?php
namespace App\Policies;

use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\{Bed, School, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use ReflectionException;

/**
 * Class BedPolicy
 * @package App\Policies
 */
class BedPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Bed|null $bed
     * @return bool
     * @throws ReflectionException
     */
    function operation(User $user, Bed $bed = null) {

        [$roomId, $studentId] = array_map(
            function ($field) use ($bed) {
                return $this->field($field, $bed);
            }, ['room_id', 'student_id']
        );
        if (isset($roomId, $studentId)) {
            $schoolId = $this->schoolId();
            $perm = School::find($schoolId)->rooms->pluck('id')->flip()->flip()->has($roomId)
                && collect($this->contactIds('student'))->flip()->has($studentId);
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}
