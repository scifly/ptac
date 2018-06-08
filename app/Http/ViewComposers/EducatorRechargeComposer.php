<?php
namespace App\Http\ViewComposers;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Educator;
use App\Models\Group;
use App\Models\School;
use App\Models\Subject;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class EducatorRechargeComposer {
    
    use ModelTrait;
    
    function __construct() { }
    
    public function compose(View $view) {
        
        $view->with(['uris' => $this->uris()]);
        
    }
    
}