<?php
namespace App\Http\Controllers;

use App\Http\Requests\MenuTypeRequest;
use App\Models\MenuType;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    function __construct(MenuType $mt) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->mt = $mt;
        
    }
    
    /**
     * 菜单类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->mt->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建菜单类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $this->authorize(
            'cs', MenuType::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存菜单类型
     *
     * @param MenuTypeRequest $request
     * @return JsonResponse|string
     * @throws AuthorizationException
     */
    public function store(MenuTypeRequest $request) {
        
        $this->authorize(
            'cs', MenuType::class
        );
        
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
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function edit($id) {
        
        $mt = $this->mt->find($id);
        $this->authorize('eud', $mt);
        
        return $this->output([
            'mt' => $mt
        ]);
        
    }
    
    /**
     * 更新菜单类型
     *
     * @param MenuType $request
     * @param $id
     * @return JsonResponse|string
     * @throws AuthorizationException
     */
    public function update(MenuType $request, $id) {
        
        $mt = $this->mt->find($id);
        $this->authorize('eud', $mt);
        
        return $this->result(
            $mt->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除菜单类型
     *
     * @param $id
     * @return JsonResponse|string
     * @throws AuthorizationException
     * @throws Exception
     */
    public function destroy($id) {
        
        $mt = $this->mt->find($id);
        $this->authorize('eud', $mt);
        
        return $this->result(
            $mt->remove($id)
        );
        
    }
    
}
