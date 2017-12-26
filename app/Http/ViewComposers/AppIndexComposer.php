<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\App;
use App\Models\Corp;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AppIndexComposer {

    use ControllerTrait;
    
    public function __construct() { }

    public function compose(View $view) {

        $user = Auth::user();
        if ($user->group->name == '运营') {
            $corps = Corp::pluck('name', 'id')->toArray();
            reset($corps);
            $apps = App::whereCorpId(key($corps))->get()->toArray();
            $this->formatDateTime($apps);
            $view->with(['corps' => $corps, 'apps' => $apps, 'uris' => $this->uris()]);
        } else {
            $corp = Corp::whereDepartmentId($user->topDeptId())->first();
            $apps = App::whereCorpId($corp->id)->get();
            $this->formatDateTime($apps);
            $view->with(['corp' => $corp, 'apps' => $apps, 'uris' => $this->uris()]);
        }

    }

    private function formatDateTime(&$apps) {

        Carbon::setLocale('zh');
        for ($i = 0; $i < sizeof($apps); $i++) {
            if ($apps[$i]['created_at']) {
                $dt = Carbon::createFromFormat('Y-m-d H:i:s', $apps[$i]['created_at']);
                $apps[$i]['created_at'] = $dt->diffForhumans();
            }
            if ($apps[$i]['updated_at']) {
                $dt = Carbon::createFromFormat('Y-m-d H:i:s', $apps[$i]['updated_at']);
                $apps[$i]['updated_at'] = $dt->diffForhumans();
            }

        }

    }

}
