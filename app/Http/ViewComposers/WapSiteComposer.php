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
    
    protected $media;
    
    /**
     * WapSiteComposer constructor.
     * @param Media $media
     */
    function __construct(Media $media) {
        
        $this->media = $media;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        if (Request::route('id')) {
            $ws = WapSite::find(Request::route('id'));
            if (!empty($ws->media_ids)) {
                $medias = $this->media->medias(explode(',', $ws->media_ids));
            }
        }
        
        $view->with([
            'medias' => $medias ?? null,
        ]);
        
    }
    
}