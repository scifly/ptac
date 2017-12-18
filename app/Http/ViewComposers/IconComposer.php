<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\IconType;
use Illuminate\Contracts\View\View;

class IconComposer {
    use ControllerTrait;
    protected $iconType;

    public function __construct(IconType $iconType) {

        $this->iconType = $iconType;

    }

    public function compose(View $view) {

        $view->with([
            'iconTypes' => $this->iconType->pluck('name', 'id'),
            'uris' => $this->uris()

        ]);

    }

}