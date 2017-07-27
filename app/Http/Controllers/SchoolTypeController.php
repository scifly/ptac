<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolTypeRequest;
use App\Models\SchoolType;
use Illuminate\Support\Facades\Request;

class SchoolTypeController extends Controller {
    
    protected $schoolType;
    
    function __construct(SchoolType $schoolType) { $this->schoolType = $schoolType; }
    
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     * @internal param SchoolType $schoolType
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->schoolType->datatable());
        }
        return view('school_type.index', [
            'js' => 'js/school/index.js',
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
        
        return view('school_type.create', [
            'js' => 'js/school_type/create.js',
            'form' => true
        ]);
        
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param SchoolTypeRequest|\Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(SchoolTypeRequest $request) {
        
        if ($this->schoolType->create($request->all())) {
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = 'wtf';
        }
        return response()->json($this->result);
        
    }
    
    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param SchoolType $schoolType
     */
    public function show($id) {
        
        return view('school_type.show', [
            'schoolType' => $this->schoolType->findOrFail($id)
        ]);
        
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param $ \Ap p\Models\SchoolType $schoolType
     */
    public function edit($id) {
        
        return view('school_type.edit', [
            'js' => 'js/school_type/edit.js',
            'schoolType' => $this->schoolType->findOrFail($id),
            'form' => true
        ]);
        
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param SchoolType $schoolType
     */
    public function update($id) {
    
        if ($this->schoolType->findOrFail($id)->update(Request::all())) {
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = 'WTF';
        }
        return response()->json($this->result);
        
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param SchoolType $schoolType
     */
    public function destroy($id) {
        
        if ($this->schoolType->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = 'Wtf';
        }
        return response()->json($this->result);
        
    }
}
