<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Media;
use App\Models\School;
use App\Models\WsmArticle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class WsmArticleComposer {
    
    use ModelTrait;
    
    protected $media;
    
    function __construct(Media $media) {
        
        $this->media = $media;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $schoolId = $this->schoolId();
        $medias = null;
        if (Request::route('id')) {
            $medias = $this->media->medias(
                explode(',', WsmArticle::find(Request::route('id'))->media_ids)
            );
        }
        $view->with([
            'wsms'   => School::find($schoolId)
                ->wapSite->wapSiteModules
                ->pluck('name', 'id')
                ->toArray(),
            'medias' => $medias,
        ]);
        
    }
    
}