<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolRequest;
use App\Models\School;
use Illuminate\Support\Facades\Request;

class SchoolController extends Controller {
    
    protected $school;
    
    function __construct(School $school) { $this->school = $school; }
    
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     * @internal param null $arg
     * @internal param Request $request
     */
    public function index() {
    
        /*if (Request::ajax() && !$arg) {
            return response()->json($this->school->datatable());
        } elseif ($arg) {
            return view('school.index', ['js' => 'js/school/index.js']);
        } else {
            return response()->json($this->school->datatable());
        }*/
        
        if (Request::get('draw')) {
            return response()->json($this->school->datatable());
        }
        return view('school.index', ['js' => 'js/school/index.js']);
    
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        return view('school.create');
        
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param SchoolRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SchoolRequest $request) {
        //
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\School $school
     * @return \Illuminate\Http\Response
     */
    public function show(School $school) {
        //
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\School $school
     * @return \Illuminate\Http\Response
     */
    public function edit(School $school) {
        //
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param SchoolRequest|\Illuminate\Http\Request $request
     * @param  \App\Models\School $school
     * @return \Illuminate\Http\Response
     */
    public function update(SchoolRequest $request, School $school) {
        //
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\School $school
     * @return \Illuminate\Http\Response
     */
    public function destroy(School $school) {
        //
    }
}
