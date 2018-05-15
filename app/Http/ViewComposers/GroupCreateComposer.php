<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;

class GroupCreateComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {

        $schools = School::find($this->schoolId())->pluck('name', 'id');
        $view->with([
            'schools' => $schools,
            'uris'    => $this->uris(),
        ]);
        
    }
    
}