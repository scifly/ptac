<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Corp;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class AppComposer
 * @package App\Http\ViewComposers
 */
class AppComposer {
    
    use ModelTrait;
    
    protected $corp;
    
    /**
     * AppComposer constructor.
     * @param Corp $corp
     */
    function __construct(Corp $corp) {
        
        $this->corp = $corp;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'titles' => [
                    '#', '名称', '类型', '所属企业', '描述', '创建于', '更新于', '状态 . 操作'
                ]
            ];
        } else {
            $data = ['corpId' => $this->corp->corpId()];
        }
        
        $view->with($data);
        
    }
    
}