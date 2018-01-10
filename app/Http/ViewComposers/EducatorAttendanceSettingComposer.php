<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;

class EducatorAttendanceSettingComposer {

    use ModelTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);

    }

}