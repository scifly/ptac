<?php
namespace App\Http\ViewComposers;

use App\Models\Department;
use Exception;
use Illuminate\Contracts\View\View;

/**
 * Class DepartmentComposer
 * @package App\Http\ViewComposers
 */
class DepartmentComposer {
    
    /**
     * @param View $view
     * @throws Exception
     */
    public function compose(View $view) {
        
        $view->with(
            (new Department)->compose()
        );
        
    }
    
}