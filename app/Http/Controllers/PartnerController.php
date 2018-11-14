<?php
namespace App\Http\Controllers;

use App\Http\Requests\PartnerRequest;
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
    
    protected $partner;
    
    /**
     * OperatorController constructor.
     * @param User $partner
     */
    function __construct(User $partner) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->partner = $partner;
        
    }
    
    /**
     * 合作伙伴列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->partner->partners()
            );
        }
        
        return $this->output();
        
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
            $this->partner->pStore(
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
        
        return $this->output([
            'partner' => $this->partner->find($id),
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
            $this->partner->pModify(
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
            $this->partner->pRemove($id)
        );
        
    }
    
}
