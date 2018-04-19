<?php
namespace App\Http\Controllers;

use App\Http\Requests\ConferenceParticipantRequest;
use App\Models\ConferenceParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 与会者
 *
 * Class ConferenceParticipantController
 * @package App\Http\Controllers
 */
class ConferenceParticipantController extends Controller {
    
    protected $cp;
    
    function __construct(ConferenceParticipant $cp) {
        
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
        
        if (Request::get('draw')) {
            return response()->json(
                $this->cp->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 保存与会者参会记录
     *
     * @param ConferenceParticipantRequest $request
     * @return JsonResponse
     */
    public function store(ConferenceParticipantRequest $request) {
        
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
