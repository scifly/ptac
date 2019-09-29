<?php
namespace App\Http\ViewComposers;

use App\Models\{Article, Column, Media, Wap};
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\{Auth, Request};
use Throwable;

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
     * @throws Throwable
     */
    public function compose(View $view) {
        
        try {
            $user = Auth::user();
            $action = explode('/', Request::path())[2];
            if ($action == 'index') {
                # 禁止学生访问微网站
                throw_if(
                    !$user || $user->role() == '学生',
                    new Exception(__('messages.unauthorized'))
                );
                throw_if(
                    !($wap = Wap::whereSchoolId(session('schoolId'))->first()),
                    new Exception(__('messages.not_found'))
                );
                $data = [
                    'wap'    => $wap,
                    'medias' => Media::whereIn('id', explode(',', $wap->media_ids))->get(),
                ];
            } elseif ($action == 'column') {
                $id = Request::input('id');
                $articles = Article::where(['column_id' => $id, 'enabled' => 1])
                    ->orderByDesc("created_at")->get();
                $data = [
                    'articles' => $articles,
                    'col'      => (new Column)->find($id),
                    'ws'       => true,
                ];
            } else {
                $id = Request::input('id');
                $article = (new Article)->find($id);
                $data = [
                    'article' => $article,
                    'medias'  => $this->media->whereIn(
                        'id', explode(',', $article->media_ids)
                    ),
                ];
            }
            $data = array_merge($data, [
                'userid' => json_decode($user->ent_attrs, true)['userid'],
            ]);
        } catch (Exception $e) {
            throw $e;
        }
        $view->with($data);
        
    }
    
}