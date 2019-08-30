<?php
namespace App\Http\ViewComposers;

use App\Models\Department;
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
        
        $view->with(
            (new Department)->compose()
        );
        
    }
    
}