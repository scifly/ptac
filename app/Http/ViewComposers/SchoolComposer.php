<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Corp;
use App\Models\SchoolType;
use Illuminate\Contracts\View\View;

class SchoolComposer {
    use ControllerTrait;
    protected $schoolType;
    protected $corp;

    public function __construct(SchoolType $schoolType, Corp $corp) {

        $this->schoolType = $schoolType;
        $this->corp = $corp;

    }

    public function compose(View $view) {

        $view->with([
            'schoolTypes' => $this->schoolType->pluck('name', 'id'),
            'corps' => $this->corp->pluck('name', 'id'),
            'uris' => $this->uris()

        ]);
    }

}