<?php
namespace App\Http\Controllers;

use App\Models\AttachmentType;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * 附件类型
 *
 * Class AttachmentTypeController
 * @package App\Http\Controllers
 */
class AttachmentTypeController extends Controller {
    
    protected $at;
    
    /**
     * AttachmentTypeController constructor.
     * @param AttachmentType $at
     */
    function __construct(AttachmentType $at) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->at = $at;
        $this->approve($at);
        
    }
    
    /**
     * 删除附件类型
     *
     * @param $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->at->remove($id)
        );
        
    }
    
}
