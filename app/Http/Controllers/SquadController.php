<?php

namespace App\Http\Controllers;

use App\Http\Requests\SquadRequest;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class SquadController extends Controller
{
    protected $squad ;

    public function __construct(Squad $squad)
    {
        $this->squad = $squad;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->squad->datatable());
        }
        return view('class.index' , [
            'js' => 'js/class/index.js',
            'dialog' => true

        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('class.create',['js' => 'js/class/create.js']);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SquadRequest $squadRequest
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(SquadRequest $squadRequest)
    {
        //
        // request
        $data['name'] = $squadRequest->input('name');
        $data['grade_id'] = $squadRequest->input('grade_id');
        $ids = $squadRequest->input('educator_ids');
        $data['educator_ids'] = implode(',', $ids);
        $data['enabled'] = $squadRequest->input('enabled');

        if(Squad::create($data))
        {
            return response()->json(['statusCode' => 200, 'message' => '添加成功!']);

        }else{
            return response()->json(['statusCode' => 202, 'message' => '添加失败!']);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Squad  $squad
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $squad = Squad::whereId($id)->first();
        $educators = User::whereHas('educator' , function($query) use ($squad) {

            $f = explode(",", $squad->educator_ids);
            $query-> whereIn('id', $f);

        })->get(['id','username'])->toArray();
        return view('class.show', ['squad' => $squad, 'educators' => $educators]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Squad  $squad
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $squad = Squad::whereId($id)->first();
        return view('class.edit', [
            'js' => 'js/class/edit.js',
            'squad' => $squad
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SquadRequest $squadRequest
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param Squad $squad
     */
    public function update(SquadRequest $squadRequest, $id)
    {
        $data = Squad::find($id);

        $data->name = $squadRequest->input('name');
        $data->grade_id = $squadRequest->input('grade_id');
        $data->educator_ids = $squadRequest->input('educator_ids');
        $data->enabled = $squadRequest->input('enabled');

        if($data->save())
        {
            return response()->json(['statusCode' => 200, 'message' => '修改成功!']);

        }else{
            return response()->json(['statusCode' => 202, 'message' => '修改失败!']);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Squad $squad
     */
    public function destroy($id)
    {
        Squad::destroy($id);

        return response()->json(['statusCode' => 200, 'Message' => 'nailed it!']);
    }
}
