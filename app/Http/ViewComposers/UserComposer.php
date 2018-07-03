<?php
namespace App\Http\ViewComposers;

use App\Models\Group;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserComposer
 * @package App\Http\ViewComposers
 */
class UserComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $rootGroupId = Group::whereName('运营')->first()->id;
        $corpGroupId = Group::whereName('企业')->first()->id;
        $schoolGroupId = Group::whereName('学校')->first()->id;
        $groups = [];
        switch (Auth::user()->group->name) {
            case '运营':
                $groups = [
                    $rootGroupId   => '运营',
                    $corpGroupId   => '企业',
                    $schoolGroupId => '学校',
                ];
                break;
            case '企业':
                $groups = [
                    $corpGroupId   => '企业',
                    $schoolGroupId => '学校',
                ];
                break;
            default:
                break;
        }
        $view->with([
            'groups' => $groups,
        ]);
        
    }
    
}