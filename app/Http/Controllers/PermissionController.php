<?php
namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\ActionGroup;
use App\Models\Group;
use App\Models\GroupMenu;
use App\Models\GroupTab;
use App\Models\Menu;
use App\Models\Tab;

class PermissionController extends Controller {
    
    protected $group, $action, $tab, $menu;
    protected $actionGroup, $groupMenu, $groupTab;
    
    function __construct(
        Group $group,
        Action $action,
        Tab $tab,
        Menu $menu,
        ActionGroup $actionGroup,
        GroupMenu $groupMenu,
        GroupTab $groupTab
    ) {
        $this->group = $group;
        $this->action = $action;
        $this->tab = $tab;
        $this->menu = $menu;
        $this->actionGroup = $actionGroup;
        $this->groupMenu = $groupMenu;
        $this->groupTab = $groupTab;
        
    }
    
}
