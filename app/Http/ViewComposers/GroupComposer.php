<?php
namespace App\Http\ViewComposers;

use App\Models\Action;
use App\Models\Tab;
use Illuminate\Contracts\View\View;

class GroupComposer {
    
    protected $tab, $action;
    
    public function __construct(Tab $tab, Action $action) {
        
        $this->tab = $tab;
        $this->action = $action;
        
    }
    
    public function compose(View $view) {
        
        $tabActions = [];
        $tabs = $this->tab->all();
        foreach ($tabs as $tab) {
            $actions = $this->action->where('controller', $tab->controller)->get(['id', 'name']);
            $actionList = [];
            foreach ($actions as $action) {
                $actionList[] = ['id' => $action->id, 'name' => $action->name];
            }
            $tabActions[] = [
                'tab'     => ['id' => $tab->id, 'name' => $tab->name],
                'actions' => $actionList,
            ];
        }
        $view->with([
            'tabActions' => $tabActions,
        ]);
    }
    
}