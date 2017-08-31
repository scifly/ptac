<?php

namespace App\Http\ViewComposers;

use App\Models\Group;
use Illuminate\Contracts\View\View;

class CustodianComposer {

    protected $group;
    
    public function __construct(Group $group) {
        
        $this->group = $group;
        
    }
    
    public function compose(View $view) {
    
        $view->with(['groups' => $this->group->pluck('name', 'id')]);
        
    }
    
}