<?php
namespace App\Http\ViewComposers;

use App\Models\DepartmentType;
use Illuminate\Contracts\View\View;

/**
 * Class DepartmentComposer
 * @package App\Http\ViewComposers
 */
class DepartmentComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'departmentTypes' => DepartmentType::pluck('name', 'id'),
        ]);
        
    }
    
}