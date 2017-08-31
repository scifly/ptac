<?php
namespace App\Http\ViewComposers;

use App\Models\ActionType;
use App\Models\Corp;
use App\Models\School;
use Illuminate\Contracts\View\View;

class DepartmentComposer {
    
    protected $corp, $school;
    
    public function __construct(Corp $corp, School $school) {
        
        $this->corp = $corp;
        $this->school = $school;
        
    }
    
    public function compose(View $view) {
        
        $view->with([
            'corps' => $this->corp->pluck('name', 'id'),
            'schools' => $this->school->pluck('name', 'id')
        ]);
        
    }
    
}