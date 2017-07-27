<?php

namespace App\Http\Controllers;

use App\Http\Requests\SemesterRequest;
use App\Models\Semester;
use Illuminate\Support\Facades\Request;

class SemesterController extends Controller {
    
    protected $semester;
    
    function __construct(Semester $semester) { $this->semester = $semester; }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->semester->datatable());
        }
        return view('semester.index', [
            'js' => 'js/semester/index.js',
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
        
        return view('semester.create', [
            'js' => 'js/semester/create.js',
            'form' => true
        ]);
        
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param SemesterRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SemesterRequest $request) {
        
        if ($this->semester->create($request->all())) {
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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        
        return view('semester.show', [
            'team' => $this->semester->findOrFail($id)
        ]);
        
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        return view('semester.edit', [
            'js' => 'js/semester/edit.js',
            'semester' => $this->semester->findOrFail($id),
            'form' => true
        ]);
        
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {
        
        if ($this->semester->findOrFail($id)->update(Request::all())) {
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
        
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        if ($this->semester->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
        
    }
}
