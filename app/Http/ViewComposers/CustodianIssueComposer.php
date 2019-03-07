<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

/**
 * Class CustodianIssueComposer
 * @package App\Http\ViewComposers
 */
class CustodianIssueComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {

        $classes = Squad::whereIn('id', $this->classIds())
            ->get()->pluck('name', 'id')->toArray();
        $titles = <<<HTML
<th>#</th>
<th class="text-center">家长</th>
<th class="text-center">学生</th>
<th class="text-center">学号</th>
<th>卡号</th>
HTML;

        $view->with([
            'formId' => 'formCustodian',
            'classes' => [0 => '(请选择一个班级)'] + $classes,
            'titles' => $titles,
            'columns' => 5
        ]);
        
    }
    
}