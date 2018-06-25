<?php
namespace App\Http\ViewComposers;

use App\Models\DepartmentType;
use Illuminate\Contracts\View\View;

class DepartmentComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'departmentTypes' => DepartmentType::pluck('name', 'id'),
        ]);
        
    }
    
}