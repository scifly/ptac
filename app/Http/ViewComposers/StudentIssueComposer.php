<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

/**
 * Class StudentIssueComposer
 * @package App\Http\ViewComposers
 */
class StudentIssueComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {

        $classes = Squad::whereIn('id', $this->classIds())
            ->get()->pluck('name', 'id')->toArray();
        
        $view->with([
            'classes' => [0 => '(请选择一个班级)'] + $classes
        ]);
        
    }
    
}