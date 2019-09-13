<?php
namespace App\Http\Controllers;

use App\Http\Requests\RoomRequest;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 房间
 *
 * Class RoomController
 * @package App\Http\Controllers
 */
class RoomController extends Controller {
    
    protected $room;
    
    /**
     * RoomController constructor.
     * @param Room $room
     */
    function __construct(Room $room) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->room = $room);
        
    }
    
    /**
     * 房间列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->room->index())
            : $this->output();
        
    }
    
    /**
     * 创建房间
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存房间
     *
     * @param RoomRequest $request
     * @return JsonResponse
     */
    public function store(RoomRequest $request) {
        
        return $this->result(
            $this->room->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑房间
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'room' => $this->room->find($id),
        ]);
        
    }
    
    /**
     * 更新房间
     *
     * @param RoomRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(RoomRequest $request, $id = null) {
        
        return $this->result(
            $this->room->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除房间
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->room->remove($id)
        );
        
    }
    
}
