<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustodianRequest;
use App\Models\Custodian;
use Illuminate\Support\Facades\Request;

class CustodianController extends Controller
{
    protected $custodian;

    function __construct(Custodian $custodian) {

        $this->custodian = $custodian;

    }
    /**
     * 显示监护人列表.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->custodian->datatable());
        }
        return parent::output(__METHOD__);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return parent::output(__METHOD__);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustodianRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Custodian  $custodian
     * @return \Illuminate\Http\Response
     */
    public function show(Custodian $custodian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Custodian  $custodian
     * @return \Illuminate\Http\Response
     */
    public function edit(Custodian $custodian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Custodian  $custodian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Custodian $custodian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Custodian  $custodian
     * @return \Illuminate\Http\Response
     */
    public function destroy(Custodian $custodian)
    {
        //
    }
}
