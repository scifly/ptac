<?php
namespace App\Http\Controllers;

use App\Http\Requests\ConferenceRoomRequest;
use App\Models\ConferenceRoom;
use Illuminate\Support\Facades\Request;

/**
 * 会议室
 *
 * Class ConferenceRoomController
 * @package App\Http\Controllers
 */
class ConferenceRoomController extends Controller {
    
    protected $cr;
    
    function __construct(ConferenceRoom $cr) {
        
        $this->cr = $cr;
        
    }
    
    /**
     * 会议室列表
     *
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存会议室
     *
     * @param ConferenceRoomRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ConferenceRoomRequest $request) {
        
        return $this->cr->store($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 会议室详情
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $cr = $this->cr->find($id);
        if (!$cr) { return $this->notFound(); }
        
        return $cr->remove($id)
            ? $this->succeed() : $this->fail();
        
    }
    
}
