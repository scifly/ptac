<?php
namespace App\Http\ViewComposers;

use App\Helpers\HttpStatusCode;
use App\Models\Media;
use App\Models\WapSite;
use App\Models\WapSiteModule;
use App\Models\WsmArticle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class MobileSiteComposer
 * @package App\Http\ViewComposers
 */
class MobileSiteComposer {
    
    protected $media;
    
    /**
     * MobileSiteComposer constructor.
     * @param Media $media
     */
    function __construct(Media $media) {
        
        $this->media = $media;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $user = Auth::user();
        $action = explode('/', Request::path())[2];
        switch ($action) {
            case 'index':
                # 禁止学生访问微网站
                abort_if(
                    !$user || $user->role() == '学生',
                    HttpStatusCode::UNAUTHORIZED,
                    __('messages.unauthorized')
                );
                abort_if(
                    !($wapSite = WapSite::whereSchoolId(session('schoolId'))->first()),
                    HttpStatusCode::NOT_FOUND,
                    __('messages.not_found')
                );
                $data = [
                    'wapsite' => $wapSite,
                    'medias'  => Media::whereIn('id', explode(',', $wapSite->media_ids))->get(),
                ];
                break;
            case 'module':
                $id = Request::input('id');
                $articles = WsmArticle::whereWsmId($id)
                    ->where('enabled', 1)
                    ->orderByDesc("created_at")
                    ->get();
                $data = [
                    'articles' => $articles,
                    'module'   => (new WapSiteModule)->find($id),
                    'ws'       => true,
                ];
                break;
            default:
                $id = Request::input('id');
                $article = (new WsmArticle)->find($id);
                $data = [
                    'article' => $article,
                    'medias'  => $this->media->whereIn(
                        'id', explode(',', $article->media_ids)
                    ),
                ];
                break;
        }
        
        $view->with($data);
        
    }
    
}