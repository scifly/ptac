<?php
namespace App\Http\ViewComposers;

use App\Models\Group;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class GroupEditComposer {
    
    protected $group;
    
    public function __construct(Group $group) {
        
        $this->group = $group;
        
    }
    
    public function compose(View $view) {

        $group = $this->group->find(Request::route('id'));
        $view->with([
            'selectedMenuIds' => implode(',', $group->menus->pluck('id')->toArray()),
            'selectedTabs'    => $group->tabs->pluck('id')->toArray(),
            'selectedActions' => $group->actions->pluck('id')->toArray(),
        ]);
        
    }
    
}