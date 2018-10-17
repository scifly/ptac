<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\Module;
use App\Models\School;
use App\Models\Tab;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

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
        
        $role = Auth::user()->group->name;
        switch ($role) {
            case '运营':
                $schools = School::whereEnabled(1)
                        ->pluck('name', 'id')->toArray();
                break;
            case '企业':
                $schools = School::where([
                    'enabled' => 1,
                    'corp_id' => (new Corp)->corpId()
                ])->pluck('name', 'id')->toArray();
                break;
            default:
                $schools = School::find($this->schoolId())
                    ->pluck('name', 'id')->toArray();
                break;
        }
        $tabs = Tab::where(['enabled' => 1, 'category' => 1])->pluck('comment', 'id')->toArray();
        if (Route::has('id')) {
            $media = Module::find(Request::route('id'))->media;
        }
        
        $view->with([
            'schools' => $schools,
            'tabs' => $tabs,
            'media' => $media ?? null
        ]);
        
    }
    
}