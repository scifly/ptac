<?php
namespace App\Http\Controllers;

use App\Http\Requests\MenuTypeRequest;
use App\Models\MenuType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 菜单类型
 *
 * Class MenuTypeController
 * @package App\Http\Controllers
 */
class MenuTypeController extends Controller {
    
    protected $mt;
    
    /**
     * MenuTypeController constructor.
     * @param MenuType $mt
     */
    function __construct(MenuType $mt) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->mt = $mt;
        $this->approve($mt);
        
    }
    
    /**
     * 菜单类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->mt->index())
            : $this->output();
        
    }
    
    /**
     * 创建菜单类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存菜单类型
     *
     * @param MenuTypeRequest $request
     * @return JsonResponse|string
     */
    public function store(MenuTypeRequest $request) {
        
        return $this->result(
            $this->mt->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑菜单类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'mt' => $this->mt->find($id),
        ]);
        
    }
    
    /**
     * 更新菜单类型
     *
     * @param MenuType $request
     * @param $id
     * @return JsonResponse|string
     */
    public function update(MenuType $request, $id) {
        
        return $this->result(
            $this->mt->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除菜单类型
     *
     * @param $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->mt->remove($id)
        );
        
    }
    
}
