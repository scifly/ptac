<?php
namespace App\Http\ViewComposers;

use App\Models\Action;
use App\Models\ActionType;
use App\Models\Tab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class ActionComposer
 * @package App\Http\ViewComposers
 */
class ActionComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $actionTypeIds = explode(
            ',', Action::find(Request::route('id'))->action_type_ids
        );
        $selectedActionTypes = [];
        foreach ($actionTypeIds as $actionTypeId) {
            $actionType = ActionType::find($actionTypeId)->toArray();
            $selectedActionTypes[$actionTypeId] = $actionType['name'];
        }
        $view->with([
            'actionTypes'         => ActionType::pluck('name', 'id'),
            'tabs'                => Tab::pluck('name', 'id'),
            'selectedActionTypes' => $selectedActionTypes ?? null,
        ]);
        
    }
    
}