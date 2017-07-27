<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppRequest;
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
        return view('app.index', ['js' => 'js/app/index.js','dialog' => true]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app.create',[
            'js' => 'js/app/create.js',
            'form' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(AppRequest $request)
    {
        //添加新数据
        $res = App::create($request->all());
        if ($res) {
            return response()->json(['statusCode' => 200, 'message' => '创建成功！']);
        }else{
            return response()->json(['statusCode' => 500, 'message' => '创建失败！']);
        }
//        // create a new record
//        $app = new App;
//        // assign the values to corresponding fields
//        $app->name = $request->name;
//        $app->description = $request->description;
//        $app->agentid = $request->agentid;
//        $app->url = $request->url;
//        $app->token = $request->token;
//        $app->encodingaeskey = $request->encodingaeskey;
//        $app->report_location_flag = $request->report_location_flag;
//        $app->logo_mediaid = $request->logo_mediaid;
//        $app->redirect_domain = $request->redirect_domain;
//        $app->isreportuser = $request->isreportuser;
//        $app->isreportenter = $request->isreportenter;
//        $app->home_url = $request->home_url;
//        $app->chat_extension_url = $request->chat_extension_url;
//        $app->menu = $request->menu;
//        $app->enabled = $request->enabled;
//        // save the record
//        if ($app->save()) {
//            return response()->json(['statusCode' => 200, 'message' => '创建成功！']);
//        }
//
//        return response()->json(['statusCode' => 500, 'message' => '创建失败！']);
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     * @internal param App $app
     */
    public function show($id)
    {
        // find the record by $id
        $app = App::find($id);
        return view('app.show', ['app' => $app]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\App  $app
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // find the record by $id
        $app = App::find($id);
        //记录返回给view
        return view('app.edit',['js' => 'js/app/edit.js', 'app' => $app]);
    }

    /**
     * Update the specified resource in storage.
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param App $app
     */
    public function update(AppRequest $request, $id)
    {
        $app = App::findOrFail($id);
        $res = $app->update($request->all());
        if ($res) {
            return response()->json(['statusCode' => 200, 'message' => '编辑成功！']);
        }else{
            return response()->json(['statusCode' => 500, 'message' => '编辑失败！']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\App  $app
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $app = App::findOrFail($id);
        if ($app->delete()){
            return response()->json(['statusCode' => 200, 'message' => '删除成功！']);
        }else{
            return response()->json(['statusCode' => 200, 'message' => '删除失败！']);
        }
    }
}
