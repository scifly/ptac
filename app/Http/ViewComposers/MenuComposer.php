<?php
namespace App\Http\ViewComposers;

use App\Helpers\Constant;
use App\Models\{Group, Icon, Menu, MenuTab, Tab};
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\{Auth, DB, Request};

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
    
        $action = explode('/', Request::path())[1];
        if ($action == 'sort') {
            $menuId = Request::route('id');
            $tabIds = MenuTab::whereMenuId($menuId)
                ->get()->sortBy('tab_order')
                ->pluck('tab_id')->toArray();
            $tabIds_ordered = implode(',', $tabIds);
            $data = [
                'tabs'   => Tab::whereIn('id', $tabIds)
                    ->orderByRaw(DB::raw("FIELD(id, $tabIds_ordered)"))
                    ->get(),
                'menuId' => $menuId,
            ];
        } else {
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
            $data = [
                'tabs'         => $tabs->pluck('comment', 'id')->toArray(),
                'icons'        => $this->icon->icons(),
                'selectedTabs' => $selectedTabs ?? null,
            ];
        }
        
        $view->with($data);
        
    }
    
}