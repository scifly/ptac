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
     * 显示成绩统计项列表
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
     * 显示创建成绩统计项的表单
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
     * 保存新创建的成绩统计项
     *
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
     * 显示指定的成绩统计项
     *
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
     * 显示编辑指定成绩统计项的表单
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
     * 更新指定的成绩统计项
     *
     * @param ScoreRangeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
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
     * 删除指定的成绩统计项
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
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

    /**
     * 显示成绩统计页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function statisticsShow(){
        $grades = DB::table('grades')->pluck('name', 'id');
        $classes = DB::table('classes')->pluck('name', 'id');
        $exams = DB::table('exams')->pluck('name', 'id');
        return view('score_range.statistics_show',[
            'js' => 'js/score_range/statistics_show.js',
            'grades' => $grades,
            'classes' => $classes,
            'exams' => $exams,
            'form' => true
        ]);
    }


    /**
     * 展示统计成绩段
     *
     * @param HttpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(HttpRequest $request){
        //获取请求参数
        $request = $request->all();
        //查询班级
        if($request['type'] == 'grade'){
            $classes = DB::table('classes')->where('grade_id', $request['grade_id'])->select('id', 'grade_id')->pluck('id')->toArray();
        }else{
            $classes = [$request['class_id']];
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
            if(count($item) != 0){
                $v->precentage = round( $v->number/count($item) * 100 , 2);
            }

        }
        return response()->json($score_range);
    }
}
