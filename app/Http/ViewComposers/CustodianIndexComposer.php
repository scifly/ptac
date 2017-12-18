<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Grade;
use App\Models\Group;
use App\Models\School;
use App\Models\Squad;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\View;

class CustodianIndexComposer {

    use ControllerTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);

    }

}