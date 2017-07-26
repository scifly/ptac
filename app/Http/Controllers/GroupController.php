<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use Illuminate\Support\Facades\Request;

class GroupController extends Controller {
    
    protected $group;
    
    /**
     * GroupController constructor.
     * @param Group $group
     */
    function __construct(Group $group) { $this->group = $group; }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->group->datatable());
        }
        return view('group.index', [
            'js' => 'js/group/index.js',
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
        
        return view('group.create', [
            'js' => 'js/group/create.js',
            'form' => true
        ]);
        
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param GroupRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupRequest $request) {
        
        if ($this->group->create($request->all())) {
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
     */
    public function show($id) {
        
        return view('group.show', [
            'group' => $this->group->findOrFail($id)
        ]);
        
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Group $group
     */
    public function edit($id) {
    
        return view('group.edit', [
            'js' => 'js/group/edit.js',
            'group' => $this->group->findOrFail($id),
            'form' => true
        ]);
    
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Group $group
     */
    public function update($id) {
        
        if ($this->group->findOrFail($id)->update(Request::all())) {
            return response()->json([
                'statusCode' => 200, 'message' => '保存成功',
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
     * @internal param Group $group
     */
    public function destroy($id) {
    
        if ($this->group->findOrFail($id)->delete()) {
            return response()->json([
                'statusCode' => 200, 'message' => '保存成功',
            ]);
        }
        return response()->json([
            'statusCode' => 500, 'message' => '保存失败'
        ]);
        
    }
    
}
