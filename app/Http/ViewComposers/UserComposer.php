<?php
namespace App\Http\ViewComposers;

use App\Helpers\Constant;
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
    
        $values = Constant::SUPER_ROLES;
        $ids = array_map(
            function ($name) {
                return Group::whereName($name)->first()->id;
            }, $values
        );
        $role = Auth::user()->role();
        if ($role == '运营') {
            $groups = array_combine($ids, $values);
        } elseif ($role == '企业') {
            $groups = array_combine(
                [$ids[1], $ids[2]],
                [$values[1], $values[2]]
            );
        }
        $view->with(['groups' => $groups ?? []]);
        
    }
    
}