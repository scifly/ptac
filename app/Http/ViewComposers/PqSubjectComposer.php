<?php
namespace App\Http\ViewComposers;

use App\Models\PollQuestionnaire;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class PqSubjectComposer
 * @package App\Http\ViewComposers
 */
class PqSubjectComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $action = explode('/', Request::path())[1];
        $data = $action == 'index'
            ? ['titles' => ['#', '题目名称', '所属问卷', '题目类型', '创建于', '更新于', '操作']]
            : [
                'pq'           => PollQuestionnaire::pluck('name', 'id'),
                'subject_type' => [0 => '单选', 1 => '多选', 2 => '填空'],
            ];
        
        $view->with($data);
        
    }
    
}