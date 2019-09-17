<?php
namespace App\Http\ViewComposers;

use App\Models\PollTopic;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class PqChoiceComposer
 * @package App\Http\ViewComposers
 */
class PqChoiceComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        $data = $action == 'index'
            ? ['titles' => ['#', '题目名称', '选项内容', '选项编号', '创建于', '更新于', '操作']]
            : ['pqs' => PollTopic::pluck('subject', 'id')];
        
        $view->with($data);
        
    }
    
}