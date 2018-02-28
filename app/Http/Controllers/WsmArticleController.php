<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\WsmArticleRequest;
use App\Models\Media;
use App\Models\WsmArticle;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 微网站文章
 *
 * Class WsmArticleController
 * @package App\Http\Controllers
 */
class WsmArticleController extends Controller {
    
    protected $wsma, $media;
    
    function __construct(WsmArticle $wsma, Media $media) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->wsma = $wsma;
        $this->media = $media;
        
    }
    
    /**
     * 微网站文章列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->wsma->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建微网站文章
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存微网站文章
     *
     * @param WsmArticleRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(WsmArticleRequest $request) {
        
        return $this->result(
            $this->wsma->store($request)
        );
        
    }
    
    /**
     * 微网站文章详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $article = WsmArticle::find($id);
        abort_if(!$article, HttpStatusCode::NOT_FOUND);
        
        return $this->output([
            'article' => $article,
            'medias'  => $this->media->medias(explode(',', $article->media_ids)),
        ]);
        
    }
    
    /**
     * 编辑微网站文章
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $article = WsmArticle::find($id);
        abort_if(!$article, HttpStatusCode::NOT_FOUND);
        
        return $this->output([
            'article' => $article,
            'medias'  => $this->media->medias(explode(',', $article->media_ids)),
        ]);
        
    }
    
    /**
     * 更新微网站文章
     *
     * @param WsmArticleRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(WsmArticleRequest $request, $id) {
        
        $article = WsmArticle::find($id);
        abort_if(!$article, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $article->modify($request, $id)
        );
        
    }
    
    /**
     * 删除微网站文章
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $article = WsmArticle::find($id);
        abort_if(!$article, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $article->delete()
        );
        
    }

    
}
