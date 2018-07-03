<?php
namespace App\Http\Controllers;

use App\Http\Requests\ConferenceRoomRequest;
use App\Models\ConferenceRoom;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 会议室
 *
 * Class ConferenceRoomController
 * @package App\Http\Controllers
 */
class ConferenceRoomController extends Controller {
    
    protected $cr;
    
    /**
     * ConferenceRoomController constructor.
     * @param ConferenceRoom $cr
     */
    function __construct(ConferenceRoom $cr) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->cr = $cr;
        $this->approve($cr);
        
    }
    
    /**
     * 会议室列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->cr->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建会议室
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存会议室
     *
     * @param ConferenceRoomRequest $request
     * @return JsonResponse
     */
    public function store(ConferenceRoomRequest $request) {
        
        return $this->result(
            $this->cr->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑会议室
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'cr' => $this->cr->find($id),
        ]);
        
    }
    
    /**
     * 更新会议室
     *
     * @param ConferenceRoomRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(ConferenceRoomRequest $request, $id = null) {
        
        return $this->result(
            $this->cr->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除会议室
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->cr->remove($id)
        );
        
    }
    
}
