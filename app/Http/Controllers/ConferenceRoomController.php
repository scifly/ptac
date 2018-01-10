<?php
namespace App\Http\Controllers;

use App\Http\Requests\ConferenceRoomRequest;
use App\Models\ConferenceRoom;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
        
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
                ConferenceRoom::datatable()
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
        
        $this->authorize('c', ConferenceRoom::class);
        
        return $this->output();
        
    }
    
    /**
     * 保存会议室
     *
     * @param ConferenceRoomRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ConferenceRoomRequest $request) {
        
        $this->authorize('c', ConferenceRoom::class);
        
        return $this->result(ConferenceRoom::store($request->all()));
        
    }
    
    /**
     * 会议室详情
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $cr = ConferenceRoom::find($id);
        $this->authorize('rud', $cr);
        
        return $this->output(['cr' => $cr]);
        
    }
    
    /**
     * 编辑会议室
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $cr = ConferenceRoom::find($id);
        $this->authorize('rud', $cr);
        
        return $this->output(['conferenceRoom' => $cr]);
        
    }
    
    /**
     * 更新会议室
     *
     * @param ConferenceRoomRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ConferenceRoomRequest $request, $id) {
        
        $cr = ConferenceRoom::find($id);
        $this->authorize('rud', $cr);
        
        return $this->result($cr->modify($request->all(), $id));
        
    }
    
    /**
     * 删除会议室
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $cr = ConferenceRoom::find($id);
        $this->authorize('rud', $cr);
        
        return $this->result($cr->remove($id));
        
    }
    
}
