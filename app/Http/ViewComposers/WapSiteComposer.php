<?php
namespace App\Http\ViewComposers;

use App\Models\Media;
use App\Models\WapSite;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class WapSiteComposer {
    
    protected $media;
    
    function __construct(Media $media) {
        
        $this->media = $media;
        
    }
    
    public function compose(View $view) {
        
        $medias = null;
        if (Request::route('id')) {
            $ws = WapSite::find(Request::route('id'));
            $medias = !empty($ws->media_ids)
                ? $this->media->medias(explode(',', $ws->media_ids))
                : null;
        }
        $view->with([
            'medias' => $medias,
        ]);
        
    }
    
}