<?php
namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 标签管理
 *
 * Class TagController
 * @package App\Http\Controllers
 */
class TagController extends Controller {
    
    protected $tag;
    
    /**
     * TagController constructor.
     * @param Tag $tag
     */
    public function __construct(Tag $tag) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->tag = $tag;
        $this->approve($tag);
        
    }
    
    /**
     * 标签列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->tag->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建标签
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存标签
     *
     * @param TagRequest $request
     * @return JsonResponse
     */
    public function store(TagRequest $request) {
        
        return $this->result(
            $this->tag->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑标签
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'tag' => Tag::find($id),
        ]);
        
    }
    
    /**
     * 更新标签
     *
     * @param TagRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(TagRequest $request, $id) {
        
        return $this->result(
            $this->tag->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除标签
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->tag->remove($id)
        );
        
    }
    
}
