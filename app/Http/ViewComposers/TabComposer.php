<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Action;
use App\Models\Icon;
use Illuminate\Contracts\View\View;

class TabComposer {

    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'icons' => Icon::icons(),
            'actions' => Action::actions(),
            'groups' => [
                0 => '所有',
                1 => '运营',
                2 => '企业',
                3 => '学校'
            ],
            'uris' => $this->uris()
        ]);

    }

}