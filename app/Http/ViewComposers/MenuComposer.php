<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Icon;
use App\Models\Tab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class MenuComposer {

    use ModelTrait;

    public function compose(View $view) {

        $role = Auth::user()->group->name;
        $tabs = null;
        switch ($role) {
            case '运营':
                $tabs = Tab::whereEnabled(1)
                    ->pluck('name', 'id');
                break;
            case '企业':
                $tabs = Tab::whereEnabled(1)
                    ->where('group_id', '<>', 1)
                    ->pluck('name', 'id');
                break;
            case '学校':
                $tabs = Tab::whereEnabled(1)
                    ->whereIn('group_id', [0, 3])
                    ->pluck('name', 'id');
                break;
            default:
                break;
        }
        $view->with([
            'tabs' => $tabs,
            'icons' => Icon::icons(),
            'uris' => $this->uris()
        ]);

    }

}