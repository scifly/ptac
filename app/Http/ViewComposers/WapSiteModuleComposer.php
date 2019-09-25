<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\WapSite;
use App\Models\WapSiteModule;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class WapSiteModuleComposer
 * @package App\Http\ViewComposers
 */
class WapSiteModuleComposer {
    
    use ModelTrait;
    
    /** @param View $view */
    public function compose(View $view) {
    
        if (explode('/', Request::path())[1] == 'index') {
            $data = [
                'titles' => ['#', '栏目名称', '所属网站', '创建于', '更新于', '状态 . 操作'],
            ];
        } else {
            if (Request::route('id')) {
                $media = WapSiteModule::find(Request::route('id'))->media;
            }
            $data = [
                'wapSites' => WapSite::whereSchoolId($this->schoolId())->pluck('site_title', 'id'),
                'media'    => $media ?? null,
            ];
        }
        
        $view->with($data);
        
    }
    
}