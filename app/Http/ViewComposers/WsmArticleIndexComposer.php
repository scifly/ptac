<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\WapSiteModule;
use Illuminate\Contracts\View\View;

class WsmArticleIndexComposer {
    use ControllerTrait;

    /**
     * @param View $view
     */
    public function compose(View $view) {
        $view->with(['uris' => $this->uris()]);
    }

}