<?php

namespace App\Http\Controllers;

use App\Models\Educator;
use Illuminate\Support\Facades\Request;

class EducatorController extends Controller
{

    protected $educator;

    public function __construct(Educator $educator)
    {
        $this->educator = $educator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Request::get('draw')) {
            return response()->json($this->educator->datatable());
        }
        return view('educator.index' , ['js' => 'js/educator/index.js']);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('educator.create',['js' => 'js/educator/create.js']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        //

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Educator  $educator
     * @return \Illuminate\Http\Response
     */
    public function show(Educator $educator)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Educator  $educator
     * @return \Illuminate\Http\Response
     */
    public function edit(Educator $educator)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Educator  $educator
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Educator $educator)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Educator  $educator
     * @return \Illuminate\Http\Response
     */
    public function destroy(Educator $educator)
    {
        //
    }
}
