<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppRequest;
use App\Models\App;
use Illuminate\Support\Facades\Request;

class AppController extends Controller {
    
    protected $app;
    
    function __construct(App $app) { $this->app = $app; }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->app->datatable());
        }
        return view('app.index', [
            'js' => 'js/app/index.js',
            'dialog' => true,
            'datatable' => true
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        return view('app.create', [
            'js' => 'js/app/create.js',
            'form' => true
        ]);
        
    }
    
    /**
     * Store a newly created resource in storage.
     * @param AppRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(AppRequest $request) {
        
        //添加新数据
        $this->app->create($request->all());
        $this->result['message'] = self::MSG_CREATE_OK;
        return response()->json($this->result);

    }
    
    /**
     * Display the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param App $app
     */
    public function show($id) {
        
        // find the record by $id
        return view('app.show', [
            'app' => $this->app->findOrFail($id)
        ]);
        
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param App $app
     */
    public function edit($id) {
        
        // find the record by $id
        return view('app.edit', [
            'js' => 'js/app/edit.js',
            'app' => $this->app->findOrFail($id),
            'form' => true
        ]);
    }
    
    /**
     * Update the specified resource in storage.
     * @param AppRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AppRequest $request, $id) {
        
        $this->app->findOrFail($id)->update($request->all());
        $this->result['message'] = self::MSG_EDIT_OK;
        
        return response()->json($this->result);
        
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param App $app
     */
    public function destroy($id) {
        
        $this->app->findOrFail($id)->delete();
        $this->result['message'] = self::MSG_DEL_OK;
        
        return response()->json($this->result);
        
    }
    
}
