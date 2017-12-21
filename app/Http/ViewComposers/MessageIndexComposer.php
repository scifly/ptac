<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\App;
use App\Models\CommType;
use App\Models\MessageType;
use App\Models\School;
use App\Models\User;
use Illuminate\Contracts\View\View;

class MessageIndexComposer {
    
    use ControllerTrait;
    
    protected $app, $school;
    
    function __construct(App $app, School $school) {
        
        $this->app = $app;
        $this->school = $school;
        
    }
    
    public function compose(View $view) {
        
        $school = $this->school->find($this->school->getSchoolId());
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