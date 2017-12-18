<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;

class WapSiteComposer {
    use ControllerTrait;
    protected $schools;

    public function __construct(School $schools) {

        $this->schools = $schools;

    }

    public function compose(View $view) {

        $view->with([
            'schools' => $this->schools->pluck('name', 'id'),
            'uris' => $this->uris()

        ]);
    }

}