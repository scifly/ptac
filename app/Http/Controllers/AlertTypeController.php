<?php
namespace App\Http\Controllers;

use App\Http\Requests\AlertTypeRequest;
use App\Models\AlertType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 警告类型
 *
 * Class AlertTypeController
 * @package App\Http\Controllers
 */
class AlertTypeController extends Controller {

    protected $at;

    function __construct(AlertType $at) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->at = $at;
        
    }
    
    /**
     * 警告类型列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->at->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建警告类型
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存警告类型
     *
     * @param AlertTypeRequest $request
     * @return JsonResponse
     */
    public function store(AlertTypeRequest $request) {
        
        return $this->result(
            AlertType::create($request->all())
        );

    }
    
    /**
     * 编辑警告类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $at = AlertType::find($id);
        abort_if(!$at, self::NOT_FOUND);

        return $this->output([
            'at' => $at,
        ]);
        
    }
    
    /**
     * 更新警告类型
     *
     * @param AlertTypeRequest $request
     * @param $id
     * @return bool|JsonResponse
     */
    public function update(AlertTypeRequest $request, $id) {
        
        $at = AlertType::find($id);
        abort_if(!$at, self::NOT_FOUND);

        return $this->result(
            $at->update($request->all())
        );

    }
    
    /**
     * 删除警告类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $at = AlertType::find($id);
        abort_if(!$at, self::NOT_FOUND);

        return $this->result(
            $at->delete()
        );

    }
    
}
