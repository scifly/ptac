<?php

namespace App\Http\Controllers;

use App\Models\App;
use Illuminate\Support\Facades\Request;

class AppController extends Controller
{

    protected $app;

    function __construct(App $app) { $this->app = $app; }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->app->datatable());
        }
        return view('app.index', ['js' => 'js/app/index.js']);
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
     * @param \Illuminate\Http\Request|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\App  $app
     * @return \Illuminate\Http\Response
     */
    public function show(App $app)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\App  $app
     * @return \Illuminate\Http\Response
     */
    public function edit(App $app)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\App  $app
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, App $app)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\App  $app
     * @return \Illuminate\Http\Response
     */
    public function destroy(App $app)
    {
        //
    }
}
