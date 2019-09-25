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
    
    /** @param View $view */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'titles' => ['#', '所属栏目', '文章名称', '文章摘要', '创建于', '更新于', '状态 . 操作'],
            ];
        } else {
            if (Request::route('id')) {
                $medias = Media::whereIn(
                    'id', explode(',', WsmArticle::find(Request::route('id'))->media_ids)
                )->get();
            }
            $data = [
                'wsms'   => School::find($this->schoolId())
                    ->wapSite->wapSiteModules
                    ->pluck('name', 'id')
                    ->toArray(),
                'medias' => $medias ?? null,
            ];
        }
        
        $view->with($data);
        
    }
    
}