<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Department;
use App\Models\Operator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class OperatorEditComposer {
    use ModelTrait;

    public function compose(View $view) {

        $operator = Operator::find(Request::route('id'));
        $selectedDepartmentIds = $operator->user->departments->pluck('id')->toArray();
        $view->with([
            'selectedDepartmentIds' => implode(',', $selectedDepartmentIds),
            'selectedDepartments' => Department::selectedNodes($selectedDepartmentIds),
            'mobiles' => $operator->user->mobiles,
            'uris' => $this->uris()
        ]);

    }

}