<?php
namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Models\Department;
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
    
    protected $tag, $department;
    
    /**
     * TagController constructor.
     * @param Tag $tag
     * @param Department $department
     */
    public function __construct(Tag $tag, Department $department) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->department = $department;
        $this->approve($this->tag = $tag);
        
    }
    
    /**
     * 标签列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->tag->index())
            : $this->output();
        
    }
    
    /**
     * 创建标签
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return Request::method() == 'POST'
            ? $this->department->contacts()
            : $this->output();
        
    }
    
    /**
     * 保存标签
     *
     * @param TagRequest $request
     * @return JsonResponse
     * @throws Throwable
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
    public function edit($id = null) {
        
        if (Request::method() == 'POST') {
            return $this->department->contacts();
        }
        $tag = $this->tag->find($id);
        $tag->name = explode('.', $tag->name)[0];
        
        return $this->output([
            'tag' => $tag,
        ]);
        
    }
    
    /**
     * 更新标签
     *
     * @param TagRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
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
