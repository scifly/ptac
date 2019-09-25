<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class SubjectModuleComposer
 * @package App\Http\ViewComposers
 */
class SubjectModuleComposer {
    
    use ModelTrait;
    
    /** @param View $view */
    public function compose(View $view) {
    
        if (explode('/', Request::path())[1] == 'index') {
            $data = [
                'titles' => ['#', '科目名称', '次分类名称', '次分类权重', '创建于', '更新于', '状态 . 操作'],
            ];
        } else {
            $builder = Subject::where(['school_id' => $this->schoolId(), 'enabled' => 1]);
            $data = ['subjects' => $builder->pluck('name', 'id')];
        }
        
        $view->with($data);
        
    }
    
}