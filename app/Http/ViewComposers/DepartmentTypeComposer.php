<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class DepartmentTypeComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'uris'   => $this->uris(),
        ]);
        
    }
    
}