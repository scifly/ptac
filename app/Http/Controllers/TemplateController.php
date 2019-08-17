<?php
namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 消息模板
 *
 * Class TemplateController
 * @package App\Http\Controllers
 */
class TemplateController extends Controller {

    protected $template;
    
    /**
     * TemplateController constructor.
     * @param Template $template
     */
    function __construct(Template $template) {

        $this->middleware(['auth', 'checkrole']);
        $this->template = $template;
        
    }
    
    /**
     * 模板列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    function index() {
    
        return Request::get('draw')
            ? response()->json($this->template->index())
            : $this->output();
        
    }
    
    /**
     * 设置所属行业
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    function config() {
        
        return Request::method() == 'POST'
            ? $this->template->config()
            : $this->output();
        
    }
    
    /**
     * 删除模板
     *
     * @param null $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    function destroy($id = null) {
    
        return $this->result(
            $this->template->remove($id)
        );
    
    }
    
}
