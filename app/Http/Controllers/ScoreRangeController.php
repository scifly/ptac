<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreRangeRequest;
use App\Models\ScoreRange;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as HttpRequest;

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
        $score_range['subject_ids'] = implode(',',$score_range['subject_ids']);
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
        $subjects_arr = explode(',', $scoreRange['subject_ids']);
        $str = '';
        foreach ($subjects_arr as $val){
            $str .= ',' . Subject::findOrFail($val)['name'];
        }
        $scoreRange['subject_ids'] = substr($str,1);
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

    public function statisticsShow(){
        $grades = DB::table('grades')->pluck('name', 'id');
        return view('score_range.statistics_show',[
            'js' => 'js/score_range/statistics_show.js',
            'grades' => $grades,
            'form' => true
        ]);
    }

    public function statistics(HttpRequest $request){
        //获取请求参数
        $request = $request->all();
        //查询班级
        if($request['type'] == 'grade'){
            $classes = DB::table('classes')->where('grade_id', $request['id'])->select('id', 'grade_id')->pluck('id')->toArray();
        }else{
            $classes = [$request['id']];
        }

        //查找符合条件的所有成绩
        $score = DB::table('scores')
            ->join('students', 'students.id', '=', 'scores.student_id')
            ->whereIn('students.class_id',$classes)
            ->where('scores.exam_id', $request['exam_id'])
            ->select('scores.id', 'scores.student_id', 'scores.subject_id', 'scores.score')
            ->orderBy('scores.score', 'desc')
            ->get();
        //查找所有成绩统计项
        $score_range = DB::table('score_ranges')->select('id', 'name', 'subject_ids', 'start_score', 'end_score')->get()->toArray();
        //循环成绩项
        foreach ($score_range as $v){
            $v->number = 0;
            //统计项统计科目
            $subject_ids = explode(',', $v->subject_ids);
            //计算学生这些科目总成绩
            $item = [];
            foreach($score as $val){
                //判断该科成绩是否需要统计
                if(in_array($val->subject_id, $subject_ids)){
                    if(!isset($item[$val->student_id])){
                        $item[$val->student_id] = $val->score;
                    }else{
                        $item[$val->student_id] += $val->score;
                    }
                }
            }
            //若成绩在统计项范围内统计数量加一
            foreach ($item as $val){
                if($v->end_score>$val && $val>=$v->start_score){
                    $v->number ++;
                }
            }
            $v->precentage = round( $v->number/count($item) * 100 , 2) . "％";
        }
        dump($score_range);
    }
}
