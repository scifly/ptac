<?php
namespace App\Http\Controllers;

use App\Models\DepartmentType;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * 部门类型
 *
 * Class DepartmentTypeController
 * @package App\Http\Controllers
 */
class DepartmentTypeController extends Controller {
    
    protected $dt;
    
    /**
     * DepartmentTypeController constructor.
     * @param DepartmentType $dt
     */
    function __construct(DepartmentType $dt) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->dt = $dt;
        $this->approve($dt);
        
    }
    
    /**
     * 删除部门类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->dt->remove($id)
        );
        
    }
    
}