<?php
namespace App\Http\Controllers;

use App\Http\Requests\BedRequest;
use App\Models\Bed;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 床位
 *
 * Class BedController
 * @package App\Http\Controllers
 */
class BedController extends Controller {
    
    protected $bed;
    
    /**
     * BedController constructor.
     * @param Bed $bed
     */
    function __construct(Bed $bed) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->bed = $bed);
        
    }
    
    /**
     * 床位列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->bed->index())
            : $this->output();
        
    }
    
    /**
     * 创建床位
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存床位
     *
     * @param BedRequest $request
     * @return JsonResponse
     */
    public function store(BedRequest $request) {
        
        return $this->result(
            $this->bed->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑床位
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'building' => $this->bed->find($id),
        ]);
        
    }
    
    /**
     * 更新床位
     *
     * @param BedRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(BedRequest $request, $id = null) {
        
        return $this->result(
            $this->bed->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除床位
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->bed->remove($id)
        );
        
    }
    
}
