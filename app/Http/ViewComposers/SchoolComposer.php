<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\SchoolType;
use Illuminate\Contracts\View\View;

class SchoolComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'schoolTypes' => SchoolType::pluck('name', 'id'),
            'corps'       => Corp::pluck('name', 'id'),
            'uris'        => $this->uris(),
        ]);
        
    }
    
}