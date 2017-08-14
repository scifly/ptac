<?php

namespace App\Http\Controllers;

use App\Http\Requests\IconTypeRequest;
use App\Models\IconType;
use Illuminate\Support\Facades\Request as Request;

class IconTypeController extends Controller {
    
    protected $iconType;
    
    function __construct(IconType $iconType) { $this->iconType = $iconType; }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->iconType->datatable());
        }
        return view('icon_type.index', [
            'js' => 'js/icon_type/index.js',
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
        
        return view('icon_type.create', [
            'js' => 'js/icon_type/create.js',
            'form' => true
        ]);
        
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param IconTypeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(IconTypeRequest $request) {
        
        if ($this->iconType->create($request->all())) {
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
        
        $iconType = $this->iconType->findOrFail($id);
        return view('icon_type.show', ['action' => $iconType]);
        
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $iconType = $this->iconType->findOrFail($id);
        return view('icon_type.edit', [
            'icon' => $iconType,
            'form' => true,
            'js' => 'js/icon_type/edit.js'
        ]);
        
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param IconTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(IconTypeRequest $request, $id) {
        
        $e = $this->iconType->findOrFail($id)->update($request->all());
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
        
        if ($this->iconType->findOrFail($id)->delete()) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
        
    }
}
