<?php
namespace App\Http\ViewComposers;

use App\Models\Subject;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class SubjectComposer
 * @package App\Http\ViewComposers
 */
class SubjectComposer {
    
    protected $subject;
    
    /**
     * SubjectComposer constructor.
     * @param Subject $subject
     */
    function __construct(Subject $subject) {
        
        $this->subject = $subject;
        
    }
    
    /**
     * @param View $view
     * @throws Exception
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        $data = $action == 'index'
            ? ['titles' => ['#', '名称', '副科', '满分', '及格线', '创建于', '更新于', '状态 . 操作']]
            : array_combine(
                ['grades', 'majors', 'selectedGrades', 'selectedMajors'],
                $this->subject->compose()
            );
        
        $view->with($data);
        
    }
    
}