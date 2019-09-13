<?php
namespace App\Http\Controllers;

use App\Http\Requests\ParticipantRequest;
use App\Models\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 与会者
 *
 * Class ParticipantController
 * @package App\Http\Controllers
 */
class ParticipantController extends Controller {
    
    protected $cp;
    
    /**
     * ParticipantController constructor.
     * @param Participant $cp
     */
    function __construct(Participant $cp) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->cp = $cp;
        $this->approve($cp);
        
    }
    
    /**
     * 与会者列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->cp->index())
            : $this->output();
        
    }
    
    /**
     * 保存与会者参会记录
     *
     * @param ParticipantRequest $request
     * @return JsonResponse
     */
    public function store(ParticipantRequest $request) {
        
        return $this->result(
            $this->cp->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 与会者参会详情
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        return $this->output([
            'cp' => $this->cp->find($id),
        ]);
        
    }
    
}
