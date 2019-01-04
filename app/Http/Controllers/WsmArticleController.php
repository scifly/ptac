<?php
namespace App\Http\Controllers;

use App\Http\Requests\WsmArticleRequest;
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
    
    protected $wsma;
    
    /**
     * WsmArticleController constructor.
     * @param WsmArticle $wsma
     */
    function __construct(WsmArticle $wsma) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->wsma = $wsma;
        $this->approve($wsma);
        
    }
    
    /**
     * 微网站文章列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->wsma->index())
            : $this->output();
        
    }
    
    /**
     * 创建微网站文章
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return Request::method() == 'POST'
            ? $this->wsma->import()
            : $this->output();
        
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
            $this->wsma->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑微网站文章
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return Request::method() == 'POST'
            ? $this->wsma->import()
            : $this->output([
                'article' => WsmArticle::find($id)
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
        
        return $this->result(
            $this->wsma->modify(
                $request->all(), $id
            )
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
        
        return $this->result(
            $this->wsma->remove($id)
        );
        
    }
    
}