<?php

namespace App\Http\Controllers;

use App\Http\Requests\TabRequest;
use App\Models\Tab;
use Illuminate\Support\Facades\Request;

class TabController extends Controller {
    
    protected $tab;
    
    function __construct(Tab $tab) { $this->tab = $tab; }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->tab->datatable());
        }
        return view('tab.index', [
            'js' => 'js/tab/index.js',
            'datatable' => true,
            'dialog' => true
        ]);
        
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        return view('tab.create', [
            'js' => 'js/action/create.js',
            'form' => true
        ]);
        
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param TabRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TabRequest $request) {
        
        if ($this->tab->create($request->all())) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = 'Oops';
        }
        return response()->json($this->result);
        
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        
        $tab = $this->tab->findOrFail($id);
        return view('action.show', ['tab' => $tab]);
        
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        return view('tab.edit', [
            'js' => 'js/tab/edit.js',
            'tab' => $this->tab->findOrFail($id),
            'form' => true
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
        
        $this->tab->findOrFail($id)->update($request->all());
        $this->result['message'] = self::MSG_EDIT_OK;
        
        return response()->json($this->result);
        
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $this->tab->findOrFail($id)->delete();
        $this->result['message'] = self::MSG_DEL_OK;
        
        return response()->json($this->result);
        
    }
    
}
