<?php
namespace App\Http\ViewComposers;

use App\Models\Icon;
use App\Models\Menu;
use App\Models\Tab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class MenuComposer {
    
    protected $icon;
    
    function __construct(Icon $icon) { $this->icon = $icon; }
    
    public function compose(View $view) {
        
        $role = Auth::user()->group->name;
        $tabs = null;
        switch ($role) {
            case '运营':
                $tabs = Tab::whereEnabled(1)
                    ->pluck('name', 'id');
                break;
            case '企业':
                $tabs = Tab::whereEnabled(1)
                    ->where('group_id', '<>', 1)
                    ->pluck('name', 'id');
                break;
            case '学校':
                $tabs = Tab::whereEnabled(1)
                    ->whereIn('group_id', [0, 3])
                    ->pluck('name', 'id');
                break;
            default:
                break;
        }
        $selectedTabs = [];
        if (Request::route('id')) {
            $selectedTabs = Menu::find(Request::route('id'))
                ->tabs->pluck('name', 'id')->toArray();
        }
        $view->with([
            'tabs'         => $tabs,
            'icons'        => $this->icon->icons(),
            'selectedTabs' => $selectedTabs,
        ]);
        
    }
    
}