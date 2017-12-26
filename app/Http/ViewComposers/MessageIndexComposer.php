<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\App;
use App\Models\School;
use Illuminate\Contracts\View\View;

class MessageIndexComposer {
    
    use ControllerTrait;
    
    public function compose(View $view) {
        
        $school = School::find(School::id());
        $data = App::whereEnabled(1)
            ->where('corp_id', $school->corp_id)
            ->get(['id', 'name', 'square_logo_url']);
        $apps = [];
        foreach ($data as $datum) {
            $apps[$datum['id']] = $datum['name'] . '|' . $datum['square_logo_url'];
        }
        
        $view->with([
            'apps' => $apps,
            'uris' => $this->uris()
        ]);

    }

}