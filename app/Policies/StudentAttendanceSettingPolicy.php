<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Semester;
use App\Models\StudentAttendanceSetting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class StudentAttendanceSettingPolicy
 * @package App\Policies
 */
class StudentAttendanceSettingPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param StudentAttendanceSetting|null $sas
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, StudentAttendanceSetting $sas = null, $abort = false) {
        
        abort_if(
            $abort && !$sas,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') { return true; }
        $isSasAllowed = $isGradeAllowed = $isSemesterAllowed = false;
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['store', 'update'])) {
            $gradeId = Request::input('grade_id');
            $semesterId = Request::input('semester_id');
            $isGradeAllowed = in_array($gradeId, $this->gradeIds());
            $allowedSemesterIds = Semester::whereSchoolId($this->schoolId())
                ->where('enabled', 1)->get()->pluck('id')->toArray();
            $isSemesterAllowed = in_array($semesterId, $allowedSemesterIds);
        }
        if (in_array($action, ['edit', 'update', 'delete'])) {
            $isSasAllowed = $sas->grade->school_id == $this->schoolId();
        }
        switch ($action) {
            case 'index':
            case 'create':
                return $isSuperRole ? true : $this->action($user);
            case 'store':
                return $isSuperRole
                    ? ($isGradeAllowed && $isSemesterAllowed)
                    : ($isGradeAllowed && $isSemesterAllowed && $this->action($user));
            case 'edit':
            case 'delete':
                return $isSuperRole ? $isSasAllowed : ($isSasAllowed && $this->action($user));
            case 'update':
                return $isSuperRole
                    ? ($isSasAllowed && $isGradeAllowed && $isSemesterAllowed)
                    : ($isSasAllowed && $isGradeAllowed && $isSemesterAllowed && $this->action($user));
            default:
                return false;
            
        }
        
    }
    
}
