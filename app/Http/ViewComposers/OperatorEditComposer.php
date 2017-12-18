<?php

namespace App\Http\ViewComposers;

use App\Models\Department;
use App\Models\Group;
use App\Models\Operator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class OperatorEditComposer {

    protected $group, $operator, $department;

    public function __construct(Group $group, Operator $operator, Department $department) {

        $this->group = $group;
        $this->operator = $operator;
        $this->department = $department;

    }

    public function compose(View $view) {

        $operator = $this->operator->find(Request::route('id'));
        $selectedDepartmentIds = $operator->user->departments->pluck('id')->toArray();
        $view->with([
            'selectedDepartmentIds' => implode(',', $selectedDepartmentIds),
            'selectedDepartments' => $this->department->selectedNodes($selectedDepartmentIds),
            'mobiles' => $operator->user->mobiles
        ]);

    }

}