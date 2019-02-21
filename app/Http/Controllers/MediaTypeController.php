<?php
namespace App\Http\Controllers;

use App\Models\MediaType;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * 媒体类型
 *
 * Class MediaTypeController
 * @package App\Http\Controllers
 */
class MediaTypeController extends Controller {
    
    protected $mt;
    
    /**
     * MediaTypeController constructor.
     * @param MediaType $mt
     */
    function __construct(MediaType $mt) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->mt = $mt;
        $this->approve($mt);
        
    }

    /**
     * 删除媒体类型
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