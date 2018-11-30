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
    
        $educators = Educator::where(['school_id' => $this->schoolId(), 'enabled' => 1])
            ->with('user')->get()->pluck('user.realname', 'id')->toArray();
        if (Request::route('id')) {
            $grade = Grade::find(Request::route('id'));
            $selectedEducators = $this->educator->educatorList(
                explode(',', rtrim($grade->educator_ids, ','))
            );
        }
        $view->with([
            'educators'         => $educators,
            'selectedEducators' => $selectedEducators ?? null,
        ]);
        
    }
    
}