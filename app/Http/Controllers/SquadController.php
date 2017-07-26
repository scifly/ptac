<?php

namespace App\Http\Controllers;

use App\Http\Requests\SquadRequest;
use App\Models\Squad;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SquadRequest $squadRequest)
    {
        //
        // request
        $data['name'] = $squadRequest->input('name');
        $data['grade_id'] = $squadRequest->input('grade_id');
        $data['educator_ids'] = $squadRequest->input('educator_ids');
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
    public function show(Squad $squad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Squad  $squad
     * @return \Illuminate\Http\Response
     */
    public function edit(Squad $squad)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Squad  $squad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Squad $squad)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Squad  $squad
     * @return \Illuminate\Http\Response
     */
    public function destroy(Squad $squad)
    {
        //
    }
}
