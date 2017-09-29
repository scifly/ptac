<?php
namespace App\Http\ViewComposers;

use App\Models\Action;
use App\Models\Corp;
use App\Models\Department;
use App\Models\School;
use App\Models\Tab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class GroupComposer {
    
    protected $tab, $action, $corp, $school;
    
    public function __construct(Tab $tab, Action $action, School $school) {
        
        $this->tab = $tab;
        $this->action = $action;
        $this->school = $school;
        
    }
    
    public function compose(View $view) {

        
        $tabActions = [];
        $tabs = $this->tab->all();
        foreach ($tabs as $tab) {
            $actions = $this->action->where('controller', $tab->controller)->get(['id', 'name']);
            $actionList = [];
            foreach ($actions as $action) {
                $actionList[] = ['id' => $action->id, 'name' => $action->name];
            }
            $tabActions[] = [
                'tab'     => ['id' => $tab->id, 'name' => $tab->name],
                'actions' => $actionList,
            ];
        }
        // $user = Auth::user();
        // $group = $user->group->name;
        // $departmentIds = [];

        // switch ($group) {
        //     case '运营':
        //         $schools = School::whereEnabled(1)->pluck('id', 'name');
        //         break;
        //     case '企业':
        //         foreach ($user->departments as $d) {
        //             $departmentIds[] = $d->id;
        //         }
        //         sort($departmentIds);
        //         $corpId = Corp::whereDepartmentId($departmentIds[0])->first()->id;
        //         $schools = School::whereCorpId($corpId)->where('enabled', 1)->pluck('id', 'name');
        //         break;
        //     case '学校':
        //         $departmentIds = [];
        //         foreach ($user->departments as $d)
        //         {
        //             $departmentIds[] = $d->id;
        //         }
        //         sort($departmentIds);
        //         $rootId = $departmentIds[0];
        //         $schools = School::whereDepartmentId($rootId)->first()->pluck('id', 'name');
        //         break;
        //     default: break;
        // }
        $view->with([
            'tabActions' => $tabActions,
            // 'schools' => $schools,
            // 'schoolId' => $schoolId,
        ]);
    }
    
}