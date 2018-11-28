<?php
namespace App\Http\ViewComposers;

use App\Models\Group;
use App\Models\Icon;
use App\Models\Menu;
use App\Models\Tab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class MenuComposer
 * @package App\Http\ViewComposers
 */
class MenuComposer {
    
    protected $icon;
    
    /**
     * MenuComposer constructor.
     * @param Icon $icon
     */
    function __construct(Icon $icon) { $this->icon = $icon; }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $groupIds = Group::whereIn('name', ['运营', '企业', '学校'])->pluck('id', 'name')->toArray();
        switch (Auth::user()->role()) {
            case '运营':
                $tabs = Tab::whereEnabled(1)
                    ->pluck('comment', 'id');
                break;
            case '企业':
                $tabs = Tab::whereEnabled(1)
                    ->where('group_id', '<>', $groupIds['运营'])
                    ->pluck('comment', 'id');
                break;
            case '学校':
                $tabs = Tab::whereEnabled(1)
                    ->whereIn('group_id', [0, $groupIds['学校']])
                    ->pluck('comment', 'id');
                break;
            default:
                break;
        }
        if (Request::route('id')) {
            $selectedTabs = Menu::find(Request::route('id'))
                ->tabs->pluck('comment', 'id')->toArray();
        }
        $view->with([
            'tabs'         => $tabs ?? null,
            'icons'        => $this->icon->icons(),
            'selectedTabs' => $selectedTabs ?? [],
        ]);
        
    }
    
}