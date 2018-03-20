<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class EducatorIndexComposer {

    use ModelTrait;

    public function compose(View $view) {
    
        $departments = Department::whereIn('id', $this->departmentIds(Auth::id()))
            ->pluck('name', 'id')->toArray();
    
        $view->with([
            'departments' => $departments,
            'importTemplate' => 'files/educators.xlsx',
            'title' => '导出教职员工',
            'uris' => $this->uris()
        ]);
        
    }

}