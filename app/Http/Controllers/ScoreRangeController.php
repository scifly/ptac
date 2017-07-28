<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreRangeRequest;
use App\Models\ScoreRange;
use Illuminate\Support\Facades\Request;

class ScoreRangeController extends Controller
{
    protected $scoreRange;

    function __construct(ScoreRange $scoreRange) { $this->scoreRange = $scoreRange; }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->scoreRange->datatable());
        }
        return view('score_range.index', [
            'js' => 'js/score_range/index.js',
            'dialog' => true,
            'datatable' => true
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('score_range.create',[
            'js' => 'js/score_range/create.js',
            'scoreCreateEditJs' => true,
            'form' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param ScoreRangeRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(ScoreRangeRequest $request)
    {
        //添加新数据
        $score_range = $request->all();
        $score_range['subject_ids'] = implode('|',$score_range['subject_ids']);
        $res = $this->scoreRange->create($score_range);
        if ($res) {
            return response()->json(['statusCode' => 200, 'message' => '创建成功！']);
        }else{
            return response()->json(['statusCode' => 500, 'message' => '创建失败！']);
        }
    }

    /**
     * Display the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param ScoreRange $scoreRange
     */
    public function show($id)
    {
        // find the record by $id
        $scoreRange = $this->scoreRange->findOrFail($id);
        return view('score_range.show', ['scoreRange' => $scoreRange]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param ScoreRange $scoreRange
     */
    public function edit($id)
    {
        // find the record by $id
        $scoreRange = $this->scoreRange->findOrFail($id);
        //记录返回给view
        return view('score_range.edit',[
            'js' => 'js/score_range/edit.js',
            'scoreCreateEditJs' => true,
            'scoreRange' => $scoreRange,
            'form' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param ScoreRangeRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param ScoreRange $scoreRange
     */
    public function update(ScoreRangeRequest $request, $id)
    {
        $scoreRange = $this->scoreRange->findOrFail($id);
        $score_range = $request->all();
        $score_range['subject_ids'] = implode(',',$score_range['subject_ids']);
        $res = $scoreRange->update($score_range);
        if ($res) {
            return response()->json(['statusCode' => 200, 'message' => '编辑成功！']);
        }else{
            return response()->json(['statusCode' => 500, 'message' => '编辑失败！']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param ScoreRange $scoreRange
     */
    public function destroy($id)
    {
        $scoreRange =$this->scoreRange->findOrFail($id);
        if ($scoreRange->delete()){
            return response()->json(['statusCode' => 200, 'message' => '删除成功！']);
        }else{
            return response()->json(['statusCode' => 200, 'message' => '删除失败！']);
        }
    }
}
