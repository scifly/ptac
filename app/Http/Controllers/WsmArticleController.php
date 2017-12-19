<?php
namespace App\Http\Controllers;

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
    
    protected $article;
    protected $media;
    
    public function __construct(WsmArticle $article, Media $media) {
        
        $this->middleware(['auth']);
        $this->article = $article;
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
            return response()->json($this->article->datatable());
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
     */
    public function store(WsmArticleRequest $request) {
        
        return $this->article->store($request)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 微网站文章详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $article = $this->article->find($id);
        if (!$article) { return parent::notFound(); }
        
        return $this->output([
            'article' => $article,
            'medias'  => $this->media->medias($article->media_ids),
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
        
        $article = $this->article->find($id);
        if (!$article) { return parent::notFound(); }
        
        return $this->output([
            'article' => $article,
            'medias'  => $this->media->medias($article->media_ids),
        ]);
        
    }
    
    /**
     * 更新微网站文章
     *
     * @param WsmArticleRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(WsmArticleRequest $request, $id) {
        
        return $this->article->modify($request, $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除微网站文章
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $article = $this->article->find($id);
        if (!$article) { return parent::notFound(); }
        
        return $article->delete() ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 微网站文章详情
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail($id) {
        
        $article = $this->article->find($id);
        
        return view('frontend.wap_site.article', [
            'article' => $article,
            'medias'  => $this->media->medias($article->media_ids),
        ]);
        
    }
    
}
