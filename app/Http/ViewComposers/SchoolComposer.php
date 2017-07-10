<?php
namespace App\Http\ViewComposers;

use App\Models\SchoolType;
use Illuminate\Contracts\View\View;

class SchoolComposer {
    
    protected $schoolTypes;
    
    public function __construct(SchoolType $schoolTypes) {
        
        $this->schoolTypes = $schoolTypes;
        
    }
    
    public function compose(View $view) {
        
        $view->with('schoolTypes', $this->schoolTypes->pluck('name', 'id'));
        
    }
    
}