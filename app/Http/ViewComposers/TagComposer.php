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
        
        if (Request::route('id')) {
            $tag = Tag::find(Request::route('id'));
            $targetIds = $tag->depts()->pluck('id')->toArray();
            $targetsHtml = (new Message)->targetsHtml($tag->users, $targetIds);
        }
        
        $view->with([
            'targets' => $targetsHtml ?? null,
            'targetIds' => isset($targetIds) ? implode(',', $targetIds) : ''
        ]);
        
    }
    
}