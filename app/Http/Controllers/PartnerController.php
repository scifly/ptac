<?php
namespace App\Http\Controllers;

use App\Http\Requests\PartnerRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 合作伙伴
 *
 * Class PartnerController
 * @package App\Http\Controllers
 */
class PartnerController extends Controller {
    
    protected $partner, $message;
    /**
     * OperatorController constructor.
     * @param User $partner
     * @param Message $message
     */
    function __construct(User $partner, Message $message) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->partner = $partner;
        $this->message = $message;
        
    }
    
    /**
     * 合作伙伴列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->partner->partners())
            : $this->output();
        
    }
    
    /**
     * 创建合作伙伴
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存合作伙伴
     *
     * @param PartnerRequest $request
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function store(PartnerRequest $request) {
        
        return $this->result(
            $this->partner->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑合作伙伴
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
       
        $partner = $this->partner->find($id);
        $partner->{'school_id'} = $partner->educator->school_id;
        return $this->output([
            'partner' => $partner,
        ]);
        
    }
    
    /**
     * 更新合作伙伴
     *
     * @param PartnerRequest $request
     * @param $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function update(PartnerRequest $request, $id = null) {
        
        return $this->result(
            $this->partner->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除合作伙伴
     *
     * @param $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->partner->remove($id, true)
        );
        
    }
    
    /**
     * 短信充值 & 查询
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function recharge($id) {
        
        return Request::get('draw')
            ? response()->json($this->message->sms('partner', $id))
            : (
            Request::method() == 'PUT'
                ? $this->partner->recharge($id, Request::all())
                : $this->output(['educator' => $this->partner->find($id)->educator])
            );
        
    }
    
}
