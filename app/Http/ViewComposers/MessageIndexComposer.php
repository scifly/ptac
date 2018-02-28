<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\App;
use App\Models\School;
use Illuminate\Contracts\View\View;

class MessageIndexComposer {

    use ModelTrait;

    public function compose(View $view) {

        $school = School::find($this->schoolId());
        $data = App::whereEnabled(1)
            ->where('corp_id', $school->corp_id)
            ->where('agentid', '!=', '999')
            ->get(['id', 'name', 'square_logo_url']);
        $apps = [];
        foreach ($data as $datum) {
            $apps[$datum['id']] = $datum['name'] . '|' . $datum['square_logo_url'];
        }

        $view->with([
            'apps' => $apps,
            'messageMaxSize' => env('MESSAGE_MAX_SIZE'),
            'uris' => $this->uris()
        ]);

    }

}