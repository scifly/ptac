<?php
namespace App\Http\Controllers;

use App\Http\Requests\IconTypeRequest;
use App\Models\IconType;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request as Request;
use Throwable;

/**
 * 图标类型
 *
 * Class IconTypeController
 * @package App\Http\Controllers
 */
class IconTypeController extends Controller {
    
    protected $it;
    
    function __construct(IconType $it) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->it = $it;
        
    }
    
    /**
     * 图标类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->it->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建图标类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $this->authorize(
            'cs', IconType::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存图标类型
     *
     * @param IconTypeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(IconTypeRequest $request) {
        
        $this->authorize(
            'cs', IconType::class
        );
        
        return $this->result(
            $this->it->store($request->all())
        );
        
    }
    
    /**
     * 编辑图标类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $it = IconType::find($id);
        $this->authorize('eud', $it);
        
        return $this->output([
            'it' => $it,
        ]);
        
    }
    
    /**
     * 更新图标类型
     *
     * @param IconTypeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(IconTypeRequest $request, $id) {
        
        $it = IconType::find($id);
        $this->authorize('eud', $it);
        
        return $this->result(
            $it->modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除图标类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $it = IconType::find($id);
        $this->authorize('eud', $it);
        
        return $this->result(
            $it->remove($id)
        );
        
    }
    
}
