<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Message;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class TagComposer
 * @package App\Http\ViewComposers
 */
class TagComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'titles' => ['#', '名称', '备注', '创建于', '更新于', '同步', '状态 . 操作'],
            ];
        } else {
            if (Request::route('id')) {
                $tag = Tag::find(Request::route('id'));
                $targetIds = $tag->depts()->pluck('id')->toArray();
                $targetsHtml = (new Message)->targetsHtml($tag->users, $targetIds);
            }
            $data = [
                'targets' => $targetsHtml ?? null,
                'targetIds' => isset($targetIds) ? implode(',', $targetIds) : ''
            ];
        }
        
        $view->with($data);
        
    }
    
}