<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class SquadComposer {
    
    use ModelTrait;
    
    protected $educator;
    
    function __construct(Educator $educator) {
        
        $this->educator = $educator;
        
    }
    
    public function compose(View $view) {
        
        $schoolId = $this->schoolId();
        $grades = Grade::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        $data = Educator::with('user')
            ->where('school_id', $schoolId)
            ->get()->toArray();
        $educators = [];
        if (!empty($data)) {
            foreach ($data as $v) {
                $educators[$v['id']] = $v['user']['realname'];
            }
        }
        $selectedEducators = null;
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
            'educators'         => $educators,
            'selectedEducators' => $selectedEducators,
            'uris'              => $this->uris(),
        ]);
        
    }
    
}