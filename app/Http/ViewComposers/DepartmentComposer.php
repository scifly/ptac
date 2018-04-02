<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\DepartmentType;
use Illuminate\Contracts\View\View;

class DepartmentComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'departmentTypes' => DepartmentType::pluck('name', 'id'),
            'uris'            => $this->uris(),
        ]);
        
    }
    
}