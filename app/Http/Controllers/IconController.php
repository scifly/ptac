<?php

namespace App\Http\Controllers;

use App\Http\Requests\IconRequest;
use App\Models\Icon;
use Illuminate\Support\Facades\Request as Request;

class IconController extends Controller {
    
    protected $icon;
    
    function __construct(Icon $icon) { $this->icon = $icon; }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->icon->datatable());
        }
        return view('icon.index', [
            'js' => 'js/icon/index.js',
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
        
        return view('icon.create', [
            'js' => 'js/icon/create.js',
            'form' => true
        ]);
        
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param IconRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(IconRequest $request) {
    
        if ($this->icon->create($request->all())) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
        
    }
    
    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
    
        $icon = $this->icon->findOrFail($id);
        return view('icon.show', ['action' => $icon]);
        
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $icon = $this->icon->findOrFail($id);
        return view('icon.edit', [
            'icon' => $icon,
            'form' => true,
            'js' => 'js/icon/edit.js'
        ]);
        
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param IconRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(IconRequest $request, $id) {
        
        $e = $this->icon->findOrFail($id)->update($request->all());
        if (!$e) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '保存失败';
        }
        return response()->json($this->result);
        
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
    
        if ($this->icon->findOrFail($id)->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
        
    }
}
