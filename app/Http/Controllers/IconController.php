<?php
namespace App\Http\Controllers;

use App\Http\Requests\IconRequest;
use App\Models\Icon;
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
    
    /**
     * IconController constructor.
     * @param Icon $icon
     */
    function __construct(Icon $icon) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->icon = $icon;
        $this->approve($icon);
        
    }
    
    /**
     * 图标列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->icon->index())
            : $this->output();
        
    }
    
    /**
     * 创建图标
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存图标
     *
     * @param IconRequest $request
     * @return JsonResponse
     */
    public function store(IconRequest $request) {
        
        return $this->result(
            $this->icon->store(
                $request->all()
            )
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
        
        return $this->output([
            'ico' => Icon::find($id),
        ]);
        
    }
    
    /**
     * 更新图标
     *
     * @param IconRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(IconRequest $request, $id) {
        
        return $this->result(
            $this->icon->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除图标
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->icon->remove($id)
        );
        
    }
    
}
