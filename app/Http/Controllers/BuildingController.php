<?php
namespace App\Http\Controllers;

use App\Http\Requests\BuildingRequest;
use App\Models\Building;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 楼舍
 *
 * Class BuildingController
 * @package App\Http\Controllers
 */
class BuildingController extends Controller {
    
    protected $building;
    
    /**
     * BuildingController constructor.
     * @param Building $building
     */
    function __construct(Building $building) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->building = $building);
        
    }
    
    /**
     * 楼舍列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->building->index())
            : $this->output();
        
    }
    
    /**
     * 创建楼舍
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存楼舍
     *
     * @param BuildingRequest $request
     * @return JsonResponse
     */
    public function store(BuildingRequest $request) {
        
        return $this->result(
            $this->building->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑楼舍
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'building' => $this->building->find($id),
        ]);
        
    }
    
    /**
     * 更新楼舍
     *
     * @param BuildingRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(BuildingRequest $request, $id = null) {
        
        return $this->result(
            $this->building->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除楼舍
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->building->remove($id)
        );
        
    }
    
}
