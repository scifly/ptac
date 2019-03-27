<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Educator;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class EventComposer
 * @package App\Http\ViewComposers
 */
class EventComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $schoolId = $this->schoolId();
            $educators = Educator::whereSchoolId($schoolId)
                ->where('enabled', 1)
                ->get();
            $educatorUsers = [];
            foreach ($educators as $educator) {
                $educatorUsers[$educator->id] = $educator->user->realname;
            }
            $subjects = Subject::whereSchoolId($schoolId)
                ->where('enabled', 1)
                ->pluck('name', 'id');
            $view->with([
                'educators' => $educatorUsers,
                'subjects'  => $subjects,
            ]);
        }
        
    }
    
}