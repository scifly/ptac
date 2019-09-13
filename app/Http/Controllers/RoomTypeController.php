<?php
namespace App\Http\Controllers;

use App\Http\Requests\RoomTypeRequest;
use App\Models\RoomType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 房间类型
 *
 * Class RoomTypeController
 * @package App\Http\Controllers
 */
class RoomTypeController extends Controller {
    
    protected $rt;
    
    /**
     * RoomTypeController constructor.
     * @param RoomType $rt
     */
    function __construct(RoomType $rt) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->rt = $rt);
        
    }
    
    /**
     * 房间类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->rt->index())
            : $this->output();
        
    }
    
    /**
     * 创建房间类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存房间类型
     *
     * @param RoomTypeRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(RoomTypeRequest $request) {
        
        return $this->result(
            $this->rt->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑房间类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'rt' => $this->rt->find($id),
        ]);
        
    }
    
    /**
     * 更新房间类型
     *
     * @param RoomTypeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(RoomTypeRequest $request, $id = null) {
        
        return $this->result(
            $this->rt->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除房间类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->rt->remove($id)
        );
        
    }
    
}
