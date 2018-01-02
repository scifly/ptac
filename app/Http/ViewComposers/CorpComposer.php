<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Company;
use Illuminate\Contracts\View\View;

class CorpComposer {
    
    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'companies' => Company::pluck('name', 'id'),
            'uris' => $this->uris()
        ]);

    }

}