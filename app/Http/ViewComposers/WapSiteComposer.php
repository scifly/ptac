<?php
namespace App\Http\ViewComposers;

use App\Models\Media;
use App\Models\WapSite;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class WapSiteComposer
 * @package App\Http\ViewComposers
 */
class WapSiteComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        if (Request::route('id')) {
            $ws = WapSite::find(Request::route('id'));
            $medias = Media::whereIn('id', explode(',', $ws->media_ids))->get();
        }
        
        $view->with(['medias' => $medias ?? null]);
        
    }
    
}