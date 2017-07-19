<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSchool;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller {
    
    protected $school;
    
    function __construct(School $school) { $this->school = $school; }
    
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
    
        if ($request->ajax()) {
            return response()->json($this->school->datatable($request));
        }
        return view('school.index');
    
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param CreateSchool|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSchool $request) {
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
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\School $school
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, School $school) {
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
