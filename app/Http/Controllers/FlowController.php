<?php
namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Flow;

/**
 * 审批
 *
 * Class FlowController
 * @package App\Http\Controllers
 */
class FlowController extends Controller {
    
    protected $pl, $media;
    
    /**
     * FlowController constructor.
     * @param Flow $pl
     * @param Media $media
     */
    function __construct(Flow $pl, Media $media) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->pl = $pl;
        $this->media = $media;
        
    }
    
}