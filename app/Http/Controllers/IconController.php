<?php
namespace App\Http\Controllers;


use App\Http\Requests\IconRequest;
use App\Models\Icon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request as Request;
use Throwable;

/**
 * 图标
 *
 * Class IconController
 * @package App\Http\Controllers
 */
class IconController extends Controller {
    
    protected $icon;
    
    function __construct(Icon $icon) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->icon = $icon;
    
    }
    
    /**
     * 图标列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->icon->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建图标
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $this->authorize(
            'cs', Icon::class
        );
        return $this->output();
        
    }
    
    /**
     * 保存图标
     *
     * @param IconRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(IconRequest $request) {
        
        $this->authorize(
            'cs', Icon::class
        );
        
        return $this->result(
            $this->icon->store($request->all())
        );
        
    }
    
    /**
     * 编辑图标
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $icon = Icon::find($id);
        $this->authorize('eud', $icon);
        
        return $this->output([
            'icon' => $icon,
        ]);
        
    }
    
    /**
     * 更新图标
     *
     * @param IconRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(IconRequest $request, $id) {
        
        $icon = Icon::find($id);
        $this->authorize('eud', $icon);
        
        return $this->result(
            $icon->modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除图标
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $icon = Icon::find($id);
        $this->authorize('eud', $icon);
        
        return $this->result(
            $icon->remove($id)
        );
        
    }
    
}
