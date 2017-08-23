<?php

namespace App\Http\Controllers;

use App\Http\Requests\TabRequest;
use App\Models\Action;
use App\Models\Menu;
use App\Models\Tab;
use Illuminate\Support\Facades\Request;

class TabController extends Controller {
    
    protected $tab, $action, $menu;
    
    function __construct(Tab $tab, Menu $menu, Action $action) {
        
        $this->tab = $tab;
        $this->menu = $menu;
        $this->action = $action;
        
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->tab->datatable());
        }
        return parent::output(__METHOD__);
        
    }
    
    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     * @internal param $tabId
     */
    public function create() {
        
        return parent::output(__METHOD__, ['menus' => $this->menu->leaves(1)]);
        
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param TabRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TabRequest $request) {
    
        return $this->tab->store($request) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        $tab = $this->tab->find($id);
        if (!$tab) { return parent::notFound(); };
        return parent::output(__METHOD__, ['tab' => $tab]);
        
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $tab = $this->tab->find($id);
        if (!$tab) { return parent::notFound(); }
        $tabMenus = $tab->menus;
        $selectedMenus = [];
        foreach ($tabMenus as $menu) {
            $selectedMenus[$menu->id] = $menu->name;
        }
        return parent::output(__METHOD__, [
            'tab' => $tab,
            'menus' => $this->menu->leaves(1),
            'selectedMenus' => $selectedMenus,
        ]);
        
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param TabRequest $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TabRequest $request, $id) {
        
        $tab = $this->tab->find($id);
        if (!$tab) { return parent::notFound(); }
        return $this->tab->modify($request, $id) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
    
        return $this->tab->remove($id) ? parent::succeed() : parent::fail();
        
    }
    
}
