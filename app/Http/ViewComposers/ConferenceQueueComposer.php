<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\ConferenceQueue;
use App\Models\ConferenceRoom;
use App\Models\DepartmentUser;
use App\Models\Educator;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class ConferenceQueueComposer
 * @package App\Http\ViewComposers
 */
class ConferenceQueueComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'titles' => ['#', '名称', '发起人', '会议室', '开始时间', '结束时间', '备注', '状态 . 操作'],
            ];
        } else {
            $schoolId = $this->schoolId();
            $conferenceRooms = ConferenceRoom::whereSchoolId($schoolId)
                ->pluck('name', 'id')
                ->toArray();
            $user = Auth::user();
            switch ($user->role()) {
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
            $data = [
                'conferenceRooms' => $conferenceRooms,
                'educators'       => $educators,
                'selectedEducators' => (new Educator)->educatorList(
                    ConferenceQueue::find(Request::route('id'))->educator_ids),
            ];
        }
        
        $view->with($data);
        
    }
    
}