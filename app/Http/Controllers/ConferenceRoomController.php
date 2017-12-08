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
    
    function __construct(ConferenceRoom $cr) {
    
        $this->middleware(['auth']);
        $this->cr = $cr;
        
    }
    
    /**
     * 会议室列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->cr->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建会议室
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存会议室
     *
     * @param ConferenceRoomRequest $request
     * @return JsonResponse
     */
    public function store(ConferenceRoomRequest $request) {
        
        return $this->cr->store($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 会议室详情
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $cr = $this->cr->find($id);
        if (!$cr) { return $this->notFound(); }
        
        return $this->output(__METHOD__, ['cr' => $cr]);
        
    }
    
    /**
     * 编辑会议室
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $cr = $this->cr->find($id);
        if (!$cr) { return $this->notFound(); }
        
        return $this->output(__METHOD__, ['cr' => $cr]);
        
    }
    
    /**
     * 更新会议室
     *
     * @param ConferenceRoomRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ConferenceRoomRequest $request, $id) {
        
        $cr = $this->cr->find($id);
        if (!$cr) { return $this->notFound(); }
        
        return $cr->modify($request->all(), $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除会议室
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $cr = $this->cr->find($id);
        if (!$cr) { return $this->notFound(); }
        
        return $cr->remove($id)
            ? $this->succeed() : $this->fail();
        
    }
    
}
