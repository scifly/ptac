<?php
namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Module;
use App\Models\Tab;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * 企业微信应用入口
 *
 * Class WechatController
 * @package App\Http\Controllers\Wechat
 */
class WechatController extends Controller {
    
    static $category = 2;
    
    protected $module;
    
    /**
     * WechatController constructor.
     * @param Module $module
     */
    function __construct(Module $module) {
    
        $this->middleware('wechat');
        $this->module = $module;
        
    }
    
    /**
     * 企业应用首页
     *
     * @return Factory|View
     */
    function index() {
    
        $modules = $this->module->where([
            'enabled' => 1,
            'school_id' => session('schoolId')
        ])->get();
        foreach ($modules as &$module) {
            if ($module->tab_id) {
                $tab = Tab::find($module->tab_id);
                if ($tab->action_id) {
                    $module->uri = Action::find($tab->action_id)->route;
                }
            }
        }
        
        return view('wechat.wechat.index', [
            'modules' => $modules
        ]);

    }
    
}
