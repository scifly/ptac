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

        [$roomId, $studentId, $ids] = array_map(
            function ($field) use ($bed) {
                return $this->field($field, $bed);
            }, ['room_id', 'student_id', 'ids']
        );
        $studentIds = $this->contactIds('student');
        if (isset($roomId, $studentId)) {
            $schoolId = $this->schoolId();
            $roomIds = School::find($schoolId)->rooms->pluck('id');
            $perm = $roomIds->flip()->has($roomId)
                && $studentIds->flip()->has($studentId);
        }
        !$ids ?: $perm = $this->model('Bed')
            ->whereIn('student_id', $studentIds)
            ->pluck('id')->flip()->has(array_values($ids));
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}
