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
        return view('app.create',['js' => 'js/app/create.js']);
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store()
    {
        // create a new record
        // assign the values to corresponding fields
        // save the record
        return response()->json(['statusCode' => 200, 'message' => '创建成功']);
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     * @internal param App $app
     */
    public function show()
    {
        // find the record by $id
        return view('app.show', ['app' => $app]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\App  $app
     * @return \Illuminate\Http\Response
     */
    public function edit(App $app)
    {
        // find the record by $id
        return view('app.edit',['js' => 'js/app/edit.js', 'app' => $app]);
    }

    /**
     * Update the specified resource in storage.
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param App $app
     */
    public function update()
    {
        // fin the record by $id
        // assign the values to corresponding fields
        // save the record
        return response()->json(['statusCode' => 200, 'message' => '编辑成功']);
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
