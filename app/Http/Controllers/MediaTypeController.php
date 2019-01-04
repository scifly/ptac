<?php
namespace App\Http\Controllers;

use App\Http\Requests\MediaTypeRequest;
use App\Models\MediaType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 媒体类型
 *
 * Class MediaTypeController
 * @package App\Http\Controllers
 */
class MediaTypeController extends Controller {
    
    protected $mt;
    
    /**
     * MediaTypeController constructor.
     * @param MediaType $mt
     */
    function __construct(MediaType $mt) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->mt = $mt;
        $this->approve($mt);
        
    }
    
    /**
     * 媒体类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->mt->index())
            : $this->output();
        
    }
    
    /**
     * 创建媒体类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存媒体类型
     *
     * @param MediaTypeRequest $request
     * @return JsonResponse|string
     */
    public function store(MediaTypeRequest $request) {
        
        return $this->result(
            $this->mt->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑媒体类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'mt' => $this->mt->find($id),
        ]);
        
    }
    
    /**
     * 更新媒体类型
     *
     * @param MediaTypeRequest $request
     * @param $id
     * @return JsonResponse|string
     */
    public function update(MediaTypeRequest $request, $id) {
        
        return $this->result(
            $this->mt->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除媒体类型
     *
     * @param $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->mt->remove($id)
        );
        
    }
    
}