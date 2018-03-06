<?php
namespace App\Http\Controllers;

use App\Http\Requests\SchoolTypeRequest;
use App\Models\SchoolType;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 学校类型
 *
 * Class SchoolTypeController
 * @package App\Http\Controllers
 */
class SchoolTypeController extends Controller {
    
    protected $st;
    
    function __construct(SchoolType $st) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->st = $st;
        
    }
    
    /**
     * 学校类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->st->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建学校类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $this->authorize(
            'cs', SchoolType::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存学校类型
     *
     * @param SchoolTypeRequest $request
     * @return JsonResponse|string
     * @throws AuthorizationException
     */
    public function store(SchoolTypeRequest $request) {
    
        $this->authorize(
            'cs', SchoolType::class
        );
    
        return $this->result(
            $this->st->store($request->all())
        );
        
    }
    
    /**
     * 编辑学校类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $st = SchoolType::find($id);
        $this->authorize('eud', $st);
        
        return $this->output([
            'st' => $st,
        ]);
        
    }
    
    /**
     * 更新学校类型
     *
     * @param SchoolTypeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(SchoolTypeRequest $request, $id) {
        
        $st = SchoolType::find($id);
        $this->authorize('eud', $st);
        
        return $this->result(
            $st->modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除学校类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $st = SchoolType::find($id);
        $this->authorize('eud', $st);
        
        return $this->result(
            $st->remove($id)
        );
        
    }
    
}
