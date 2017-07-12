<?php
namespace App\Http\ViewComposers;

use App\Models\Corp;
use App\Models\SchoolType;
use Illuminate\Contracts\View\View;

class SchoolComposer {
    
    protected $schoolTypes;
    protected $corps;
    
    public function __construct(SchoolType $schoolTypes, Corp $corps) {
        
        $this->schoolTypes = $schoolTypes;
        $this->corps = $corps;
        
    }
    
    public function compose(View $view) {
        
        // $view->with('schoolTypes', $this->schoolTypes->pluck('name', 'id'));
        $view->with([
            'schoolTypes' => $this->schoolTypes->pluck('name', 'id'),
            'corps' => $this->corps->pluck('name', 'id')
        ]);
    }
    
}