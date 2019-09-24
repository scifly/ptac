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
    
    protected $participant;
    
    /**
     * ParticipantController constructor.
     * @param Participant $participant
     */
    function __construct(Participant $participant) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->participant = $participant);
        
    }
    
    /**
     * 与会者列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->participant->index())
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
            $this->participant->store(
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
            'participant' => $this->participant->find($id),
        ]);
        
    }
    
}
