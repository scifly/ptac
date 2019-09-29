<?php
namespace App\Http\Controllers;

use App\Http\Requests\TabRequest;
use App\Models\Tab;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 控制器
 *
 * Class TabController
 * @package App\Http\Controllers
 */
class TabController extends Controller {
    
    protected $tab;
    
    /**
     * TabController constructor.
     * @param Tab $tab
     */
    function __construct(Tab $tab) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->tab = $tab);
        
    }
    
    /**
     * 控制器列表
     *
     * @return bool|JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            $response = response()->json($this->tab->index());
        } else {
            $this->tab->scan();
            $response = $this->output();
        }
        
        return $response;
        
    }
    
    /**
     * 编辑控制器
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'tab' => $this->tab->find($id),
        ]);
        
    }
    
    /**
     * 更新控制器
     *
     * @param TabRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(TabRequest $request, $id = null) {
        
        return $this->result(
            $this->tab->modify($request->all(), $id)
        );
        
    }
    
}
