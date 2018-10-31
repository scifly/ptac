<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Media;
use App\Models\School;
use App\Models\WsmArticle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class WsmArticleComposer
 * @package App\Http\ViewComposers
 */
class WsmArticleComposer {
    
    use ModelTrait;
    
    protected $media;
    
    /**
     * WsmArticleComposer constructor.
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
            $medias = $this->media->whereIn(
                'id', explode(',', WsmArticle::find(Request::route('id'))->media_ids)
            )->get();
        }
        
        $view->with([
            'wsms'   => School::find($this->schoolId())
                ->wapSite->wapSiteModules
                ->pluck('name', 'id')
                ->toArray(),
            'medias' => $medias ?? null,
        ]);
        
    }
    
}