<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Squad;
use App\Models\Turnstile;
use Illuminate\Contracts\View\View;

/**
 * Class StudentPermitComposer
 * @package App\Http\ViewComposers
 */
class StudentPermitComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {

        $classes = Squad::whereIn('id', $this->classIds())
            ->get()->pluck('name', 'id')->toArray();
        $turnstiles = Turnstile::whereSchoolId($this->schoolId())->get();
        $tList = [];
        foreach ($turnstiles as $t) {
            $tList[$t->id] = implode('.', [$t->sn, $t->location]);
        }
 
        $view->with([
            'formId' => 'formStudent',
            'sections' => $classes,
            'turnstiles' => $tList
        ]);
        
    }
    
}