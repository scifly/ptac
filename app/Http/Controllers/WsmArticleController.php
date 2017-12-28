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
    
    public function __construct() {
        
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 微网站文章列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(WsmArticle::datatable());
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
        
        return $this->result(WsmArticle::store($request));
        
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
        if (!$article) { return parent::notFound(); }
        
        return $this->output([
            'article' => $article,
            'medias'  => Media::medias($article->media_ids),
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
        if (!$article) { return parent::notFound(); }
        
        return $this->output([
            'article' => $article,
            'medias'  => Media::medias($article->media_ids),
        ]);
        
    }
    
    /**
     * 更新微网站文章
     *
     * @param WsmArticleRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(WsmArticleRequest $request, $id) {
        
        $article = WsmArticle::find($id);
        
        return $this->result($article->modify($request, $id));
        
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
        if (!$article) { return parent::notFound(); }
        
        return $this->result($article->delete());
        
    }
    
    /**
     * 微网站文章详情
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail($id) {
        
        $article = WsmArticle::find($id);
        
        return view('frontend.wap_site.article', [
            'article' => $article,
            'medias'  => Media::medias($article->media_ids),
        ]);
        
    }
    
}
