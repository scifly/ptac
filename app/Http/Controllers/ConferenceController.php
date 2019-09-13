<?php
namespace App\Http\Controllers;

use App\Http\Requests\ConferenceRequest;
use App\Models\Conference;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 会议
 *
 * Class ConferenceController
 * @package App\Http\Controllers
 */
class ConferenceController extends Controller {
    
    protected $conference;
    
    /**
     * ConferenceController constructor.
     * @param Conference $conference
     */
    function __construct(Conference $conference) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->conference = $conference);
        
    }
    
    /**
     * 会议列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->conference->index())
            : $this->output();
        
    }
    
    /**
     * 创建会议
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存会议
     *
     * @param ConferenceRequest $request
     * @return JsonResponse
     */
    public function store(ConferenceRequest $request) {
        
        return $this->result(
            $this->conference->store($request->all())
        );
        
    }
    
    /**
     * 编辑会议
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'cq' => $this->conference->find($id),
        ]);
        
    }
    
    /**
     * 更新会议
     *
     * @param ConferenceRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(ConferenceRequest $request, $id) {
        
        return $this->result(
            $this->conference->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除会议
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->conference->remove($id)
        );
        
    }
    
}
