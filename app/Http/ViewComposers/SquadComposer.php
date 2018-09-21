<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class SquadComposer
 * @package App\Http\ViewComposers
 */
class SquadComposer {
    
    use ModelTrait;
    
    protected $educator;
    
    /**
     * SquadComposer constructor.
     * @param Educator $educator
     */
    function __construct(Educator $educator) {
        
        $this->educator = $educator;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $grades = Grade::whereIn('id', $this->gradeIds())
            ->where('enabled', 1)
            ->pluck('name', 'id');
        $educators = Educator::whereIn('id', $this->contactIds('educator'))
            ->where('enabled', 1)->get();
        $educatorList = [];
        foreach ($educators as $educator) {
            $educatorList[$educator->id] = $educator->user->realname;
        }
        if (Request::route('id')) {
            $educatorIds = Squad::find(Request::route('id'))->educator_ids;
            if ($educatorIds != '0') {
                $selectedEducators = $this->educator->educatorList(
                    explode(',', $educatorIds)
                );
            }
        }
        $view->with([
            'grades'            => $grades,
            'educators'         => $educatorList,
            'selectedEducators' => $selectedEducators ?? null,
        ]);
        
    }
    
}