<?php
namespace App\Http\ViewComposers;

use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class ActionComposer {

    protected $action, $actionType;

    public function __construct(ActionType $actionType) {

        $this->actionType = $actionType;

    }

    public function compose(View $view) {
    
        $actionTypeIds = explode(',', Action::find(Request::route('id'))->action_type_ids);
        $selectedActionTypes = [];
        foreach ($actionTypeIds as $actionTypeId) {
            $actionType = ActionType::find($actionTypeId)->toArray();
            $selectedActionTypes[$actionTypeId] = $actionType['name'];
        }
        $view->with([
            'actionTypes' => $this->actionType->pluck('name', 'id'),
            'selectedActionTypes' => !empty($selectedActionTypes) ? $selectedActionTypes : null
        ]);

    }

}