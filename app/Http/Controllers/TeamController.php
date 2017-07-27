<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use Illuminate\Support\Facades\Request;

class TeamController extends Controller {
    
    protected $team;
    
    /**
     * TeamController constructor.
     * @param Team $team
     */
    public function __construct(Team $team) { $this->team = $team; }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->team->datatable());
        }
        return view('team.index', [
            'js' => 'js/team/index.js',
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
        
        return view('team.create', [
            'js' => 'js/team/create.js',
            'form' => true
        ]);
        
    }
    
    /**
     * Store a newly created resource in storage.
     * @param TeamRequest $request
     * @return \Illuminate\Http\Response
     * @internal param HttpRequest|HttpRequst|Request $request
     */
    public function store(TeamRequest $request) {
    
        if ($this->team->create($request->all())) {
            return response()->json([
                'statusCode' => 200, 'message' => '保存成功',
            ]);
        }
        return response()->json([
            'statusCode' => 500, 'message' => '保存失败'
        ]);
    
    }
    
    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Team $team
     */
    public function show($id) {
    
        return view('team.show', [
            'team' => $this->team->findOrFail($id)
        ]);
        
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        return view('team.edit', [
            'js' => 'js/team/edit.js',
            'team' => $this->team->findOrFail($id),
            'form' => true
        ]);
        
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {
    
        if ($this->team->findOrFail($id)->update(Request::all())) {
            return response()->json([
                'statusCode' => 200, 'message' => '保存成功'
            ]);
        }
        return response()->json([
            'statusCode' => 500, 'message' => '保存失败'
        ]);
        
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
    
        if ($this->team->findOrFail($id)->delete()) {
            return response()->json([
                'statusCode' => 200, 'message' => '删除成功'
            ]);
        }
        return response()->json([
            'statusCode' => 500, 'message' => '删除失败'
        ]);
        
    }
}
