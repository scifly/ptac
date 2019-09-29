<?php
namespace App\Http\Controllers;

use App\Http\Requests\ColumnRequest;
use App\Models\Column;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 栏目
 *
 * Class ColumnController
 * @package App\Http\Controllers
 */
class ColumnController extends Controller {
    
    protected $column;
    
    /**
     * ColumnController constructor.
     * @param Column $column
     */
    public function __construct(Column $column) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->column = $column);
        
    }
    
    /**
     * 栏目列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->column->index())
            : $this->output();
        
    }
    
    /**
     * 创建栏目
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return Request::method() == 'POST'
            ? $this->column->import()
            : $this->output();
        
    }
    
    /**
     * 保存栏目
     *
     * @param ColumnRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(ColumnRequest $request) {
        
        return $this->result(
            $this->column->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑栏目
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return Request::method() == 'POST'
            ? $this->column->import()
            : $this->output([
                'col' => $this->column->find($id)
            ]);
        
    }
    
    /**
     * 更新栏目
     *
     * @param ColumnRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(ColumnRequest $request, $id) {
        
        return $this->result(
            $this->column->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除栏目
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->column->remove($id)
        );
        
    }
    
}
