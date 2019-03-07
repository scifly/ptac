<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

/**
 * Class CustodianIssueComposer
 * @package App\Http\ViewComposers
 */
class EducatorIssueComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {

        $classes = Squad::whereIn('id', $this->classIds())
            ->get()->pluck('name', 'id')->toArray();
        $titles = <<<HTML
<th>#</th>
<th class="text-center">姓名</th>
<th class="text-center">员工编号/用户名</th>
<th>卡号</th>
HTML;

        $view->with([
            'prompt' => '教师列表',
            'formId' => 'formEducator',
            'classes' => [0 => '(请选择一个部门)'] + $classes,
            'titles' => $titles,
            'columns' => 4
        ]);
        
    }
    
}