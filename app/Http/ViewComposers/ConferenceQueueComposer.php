<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\ConferenceRoom;
use App\Models\DepartmentUser;
use App\Models\Educator;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ConferenceQueueComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $schoolId = $this->schoolId();
        $conferenceRooms = ConferenceRoom::whereSchoolId($schoolId)
            ->pluck('name', 'id')
            ->toArray();
        $user = Auth::user();
        switch ($user->group->name) {
            case '运营':
            case '企业':
            case '学校':
                $educators = Educator::whereSchoolId($schoolId)
                    ->with('user')->get()
                    ->pluck('user.realname', 'id')
                    ->toArray();
                break;
            default:
                $departmentIds = $user->departmentIds($user->id);
                $userIds = array_unique(
                    DepartmentUser::whereIn('department_id', $departmentIds)
                        ->get(['user_id'])
                        ->toArray()
                );
                $educators = [];
                foreach ($userIds as $userId) {
                    $u = User::find($userId);
                    if ($u->educator) {
                        $educators[$u->educator->id] = $u->realname;
                    }
                }
                break;
        }
        $view->with([
            'conferenceRooms' => $conferenceRooms,
            'educators'       => $educators,
        ]);
        
    }
    
}