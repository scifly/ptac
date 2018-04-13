<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\WapSiteRequest;
use App\Models\Media;
use App\Models\School;
use App\Models\WapSite;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 微网站
 *
 * Class WapSiteController
 * @package App\Http\Controllers
 */
class WapSiteController extends Controller {
    
    protected $ws, $media, $school;
    
    public function __construct(WapSite $ws, Media $media, School $school) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->ws = $ws;
        $this->media = $media;
        $this->school = $school;
        
    }
    
    /**
     * 微网站详情
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return $this->output(
            $this->ws->index()
        );
        
    }
    
    /**
     * 编辑微网站
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $ws = WapSite::find($id);
        abort_if(!$ws, HttpStatusCode::NOT_FOUND);
        
        if (Request::method() == 'POST') {
            return $this->ws->upload();
        }
        
        return $this->output([
            'ws'     => $ws,
            'medias' => !empty($ws->media_ids)
                ? $this->media->medias(explode(',', $ws->media_ids))
                : null,
        ]);
        
    }
    
    /**
     * 更新微网站
     *
     * @param WapSiteRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(WapSiteRequest $request, $id) {
        
        $ws = WapSite::find($id);
        abort_if(!$ws, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $ws->modify($request, $id)
        );
        
    }
    
}

