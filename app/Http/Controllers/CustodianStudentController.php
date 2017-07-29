<?php

namespace App\Http\Controllers;

use App\Models\CustodianStudent;
use Illuminate\Support\Facades\Request;

class CustodianStudentController extends Controller
{
    protected $custodianStudent;
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function show(CustodianStudent $custodianStudent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function edit(CustodianStudent $custodianStudent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustodianStudent $custodianStudent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustodianStudent $custodianStudent)
    {
        //
    }
}
