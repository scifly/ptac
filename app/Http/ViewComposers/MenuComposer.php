<?php
namespace App\Http\ViewComposers;

use App\Helpers\Constant;
use App\Models\{Group, Icon, Menu, Tab};
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\{Auth, Request};

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
    
        $role = Auth::user()->group->name;
        $groupIds = Group::whereIn('name', Constant::SUPER_ROLES)
            ->pluck('id', 'name')->toArray();
        $tabs = Tab::whereEnabled(1)->get();
        if ($role == '企业') {
            $tabs = $tabs->where('group_id', '<>', $groupIds['运营']);
        } elseif ($role == '学校') {
            $tabs = $tabs->whereIn('group_id', [0, $groupIds['学校']]);
        }
        if (Request::route('id')) {
            $selectedTabs = Menu::find(Request::route('id'))->tabs
                ->pluck('comment', 'id')->toArray();
        }
        $view->with([
            'tabs'         => $tabs->pluck('comment', 'id')->toArray(),
            'icons'        => $this->icon->icons(),
            'selectedTabs' => $selectedTabs ?? null,
        ]);
        
    }
    
}