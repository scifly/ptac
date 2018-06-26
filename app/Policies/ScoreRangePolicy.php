<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\ScoreRange;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class ScoreRangePolicy {
    
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
     * 权限判断
     *
     * @param User $user
     * @param ScoreRange|null $sr
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, ScoreRange $sr = null, $abort = false) {
        
        abort_if(
            $abort && !$sr,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if ($user->group->name == '运营') { return true; }
        $isSrAllowed = $isSubjectAllowed = false;
        $isSuperRole = in_array($user->group->name, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['store', 'update'])) {
            $subjectIds = Request::input('subject_ids');
            $allowedSubjectIds = Subject::whereSchoolId($this->schoolId())
                ->pluck('id')->toArray();
            $isSubjectAllowed = empty(array_diff(
                $subjectIds, array_merge([0], $allowedSubjectIds)
            ));
        }
        if (in_array($action, ['edit', 'update', 'delete'])) {
            $isSrAllowed = in_array($sr->school_id, $this->schoolIds());
        }
        switch ($action) {
            case 'index':
            case 'create':
                return $isSuperRole ? true : $this->action($user);
            case 'store':
                return $isSuperRole ? $isSubjectAllowed : ($isSubjectAllowed && $this->action($user));
            case 'edit':
            case 'delete':
                return $isSuperRole ? $isSrAllowed : ($isSrAllowed && $this->action($user));
            case 'update':
                return $isSuperRole
                    ? ($isSrAllowed && $isSubjectAllowed)
                    : ($isSrAllowed && $isSubjectAllowed & $this->action($user));
            case 'stat':
                # todo: to be refactored
            default:
                return false;
                
        }
    }
    
}
