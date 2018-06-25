<?php
namespace App\Http\ViewComposers;

use App\Models\Group;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class GroupEditComposer {
    
    public function compose(View $view) {
        
        $group = Group::find(Request::route('id'));
        $view->with([
            'selectedMenuIds'   => implode(',', $group->menus->pluck('id')->toArray()),
            'selectedTabIds'    => $group->tabs->pluck('id')->toArray(),
            'selectedActionIds' => $group->actions->pluck('id')->toArray(),
        ]);
        
    }
    
}