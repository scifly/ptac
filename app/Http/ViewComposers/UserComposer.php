<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Group;
use Illuminate\Contracts\View\View;

class UserComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {

        $view->with([
            'groups' => Group::pluck('name', 'id'),
            'uris' => $this->uris()
        ]);
        
    }

}