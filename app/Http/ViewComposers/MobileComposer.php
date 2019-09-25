<?php
namespace App\Http\ViewComposers;

use App\Helpers\Constant;
use App\Models\{Media, WapSite, WapSiteModule, WsmArticle};
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\{Auth, Request};

/**
 * Class MobileComposer
 * @package App\Http\ViewComposers
 */
class MobileComposer {
    
    protected $media;
    
    /**
     * MobileComposer constructor.
     * @param Media $media
     */
    function __construct(Media $media) {
        
        $this->media = $media;
        
    }
    
    /**
     * @param View $view
     * @throws Exception
     */
    public function compose(View $view) {
        
        try {
            $user = Auth::user();
            $action = explode('/', Request::path())[2];
            if ($action == 'index') {
                # 禁止学生访问微网站
                abort_if(
                    !$user || $user->role() == '学生',
                    Constant::UNAUTHORIZED,
                    __('messages.unauthorized')
                );
                abort_if(
                    !($wapSite = WapSite::whereSchoolId(session('schoolId'))->first()),
                    Constant::NOT_FOUND,
                    __('messages.not_found')
                );
                $data = [
                    'wapsite' => $wapSite,
                    'medias'  => Media::whereIn('id', explode(',', $wapSite->media_ids))->get(),
                ];
            } elseif ($action == 'module') {
                $id = Request::input('id');
                $articles = WsmArticle::where(['wsm_id' => $id, 'enabled' => 1])
                    ->orderByDesc("created_at")->get();
                $data = [
                    'articles' => $articles,
                    'module'   => (new WapSiteModule)->find($id),
                    'ws'       => true,
                ];
            } else {
                $id = Request::input('id');
                $article = (new WsmArticle)->find($id);
                $data = [
                    'article' => $article,
                    'medias'  => $this->media->whereIn(
                        'id', explode(',', $article->media_ids)
                    ),
                ];
            }
            $data = array_merge($data, [
                'userid' => json_decode($user->ent_attrs, true)['userid']
            ]);
        } catch (Exception $e) {
            throw $e;
        }
        
        $view->with($data);
        
    }
    
}