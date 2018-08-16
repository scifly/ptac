<?php
namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * 套餐类型
 *
 * Class DemoController
 * @package App\Http\Controllers
 */
class DemoController extends Controller {
    
    /**
     * DemoController constructor.
     */
    function __construct() { }
    
    /**
     * 演示首页
     *
     * @return Factory|View
     */
    public function index() { return view('demo.index'); }
    
    /**
     * 平安校园
     *
     * @return Factory|View
     */
    public function safe() { return view('demo.safe'); }
    
    /**
     * 智慧校园
     *
     * @return Factory|View
     */
    public function wisdom() { return view('demo.wisdom'); }
    
    /**
     * 智慧课堂
     *
     * @return Factory|View
     */
    public function classroom() { return view ('demo.classroom'); }
    
    /**
     * 信息管理
     *
     * @return Factory|View
     */
    public function info() { return view('demo.info'); }
    
}
