<?php

namespace App\Http\ViewComposers;

use App\Models\School;
use Illuminate\Contracts\View\View;

class WapSiteComposer {
    
    protected $schools;
    
    public function __construct(School $schools) {
        
        $this->schools = $schools;
        
    }
    
    public function compose(View $view) {
        
        
        $view->with([
            'schools' => $this->schools->pluck('name', 'id'),
        ]);
    }
    
}