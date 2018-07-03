<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Educator;
use App\Models\Grade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class GradeComposer
 * @package App\Http\ViewComposers
 */
class GradeComposer {
    
    use ModelTrait;
    
    protected $educator;
    
    /**
     * GradeComposer constructor.
     * @param Educator $educator
     */
    function __construct(Educator $educator) {
        
        $this->educator = $educator;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $schoolId = $this->schoolId();
        $educators = Educator::whereSchoolId($schoolId)
            ->where('enabled', 1)->get();
        $educatorUsers = [];
        foreach ($educators as $educator) {
            if ($educator->user) {
                $educatorUsers[$educator->id] = $educator->user->realname;
            }
        }
        $selectedEducators = [];
        if (Request::route('id')) {
            $grade = Grade::find(Request::route('id'));
            $selectedEducators = $this->educator->educatorList(
                explode(",", rtrim($grade->educator_ids, ","))
            );
        }
        $view->with([
            'educators'         => $educatorUsers,
            'selectedEducators' => $selectedEducators,
        ]);
        
    }
    
}