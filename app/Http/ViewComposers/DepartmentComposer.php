<?php
namespace App\Http\ViewComposers;

use App\Models\DepartmentType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class DepartmentComposer
 * @package App\Http\ViewComposers
 */
class DepartmentComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            //
        } else {
            $view->with([
                'departmentTypes' => DepartmentType::pluck('name', 'id'),
            ]);
        }
        
    }
    
}