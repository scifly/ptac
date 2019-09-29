<?php
namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 文章
 *
 * Class ArticleController
 * @package App\Http\Controllers
 */
class ArticleController extends Controller {
    
    protected $article;
    
    /**
     * ArticleController constructor.
     * @param Article $article
     */
    function __construct(Article $article) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->article = $article);
        
    }
    
    /**
     * 文章列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->article->index())
            : $this->output();
        
    }
    
    /**
     * 创建文章
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return Request::method() == 'POST'
            ? $this->article->import()
            : $this->output();
        
    }
    
    /**
     * 保存文章
     *
     * @param ArticleRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(ArticleRequest $request) {
        
        return $this->result(
            $this->article->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑文章
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return Request::method() == 'POST'
            ? $this->article->import()
            : $this->output([
                'article' => Article::find($id)
            ]);
        
    }
    
    /**
     * 更新文章
     *
     * @param ArticleRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(ArticleRequest $request, $id) {
        
        return $this->result(
            $this->article->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除文章
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->article->remove($id)
        );
        
    }
    
}