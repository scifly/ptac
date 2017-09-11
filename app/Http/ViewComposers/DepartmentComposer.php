<?php
namespace App\Http\ViewComposers;

use App\Models\DepartmentType;
use Illuminate\Contracts\View\View;

class DepartmentComposer {
    
    protected $departmentType;
    
    public function __construct(DepartmentType $departmentType) {

        $this->departmentType = $departmentType;
        
    }
    
    public function compose(View $view) {
        
        $view->with([
            'departmentTypes' => $this->departmentType->pluck('name', 'id'),
        ]);
        
    }
    
}