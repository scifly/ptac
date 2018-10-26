<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Group;
use App\Models\Module;
use App\Models\School;
use App\Models\Tab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class ExamIndexComposer
 * @package App\Http\ViewComposers
 */
class ModuleComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        
        $groups = [
            0 => '公用',
            Group::whereName('监护人')->first()->id => '监护人',
        ];
        $superGroups = [
            Group::whereName('运营')->first()->id => '运营',
            Group::whereName('企业')->first()->id => '企业',
            Group::whereName('学校')->first()->id => '学校',
        ];
        switch (Auth::user()->role()) {
            case '运营':
                $schools = School::whereEnabled(1)->get();
                break;
            case '企业':
                $schools = School::where(['corp_id' => (new Corp)->corpId(), 'enabled' => 1])->get();
                break;
            default:
                $schools = School::find($this->schoolId());
                break;
        }
        $schoolList = $schools->pluck('name', 'id')->toArray();
        ksort($schoolList);
        $groups += Group::where(['enabled' => 1, 'school_id' => key($schoolList)])
            ->pluck('name', 'id')->toArray();
        $groups += $superGroups;
        $tabs = Tab::where(['enabled' => 1, 'category' => 1])->get();
        if (Request::route('id')) {
            $media = Module::find(Request::route('id'))->media;
        }
        
        $view->with([
            'schools' => $schoolList,
            'groups' => $groups,
            'tabs' => [null => ''] + $tabs->pluck('comment', 'id')->toArray(),
            'media' => $media ?? null
        ]);
        
    }
    
}