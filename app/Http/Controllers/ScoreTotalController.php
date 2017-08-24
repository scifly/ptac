<?php

namespace App\Http\Controllers;

use App\Models\ScoreTotal;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ScoreTotalController extends Controller {

    protected $score_total;

    function __construct(ScoreTotal $score_total) {
        $this->score_total = $score_total;
    }

    /**
     * 显示总成绩列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->score_total->datatable());
        }
        return view('score_total.index', [
            'js' => 'js/score_total/index.js',
            'dialog' => true,
            'datatable' => true,
        ]);
    }

    /**
     * 显示总成绩记录详情
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id) {
        $score_total = $this->score_total->findOrFail($id);
        $studentname = User::whereId($score_total->student->user_id)->get(['realname'])->first();
        $subjects_arr = explode(',', $score_total['subject_ids']);
        $subjects = Subject::whereIn('id', $subjects_arr)->get(['name']);
        $na_subjects_arr = explode(',', $score_total['na_subject_ids']);
        $na_subjects = Subject::whereIn('id', $na_subjects_arr)->get(['name']);

        return view('score_total.show', [
            'score_total' => $score_total,
            'studentname' => $studentname,
            'subjects' => $subjects,
            'na_subjects' => $na_subjects
        ]);
    }

    /**
     * 总成绩统计
     *
     * @param $exam_id
     */
    public function statistics($exam_id){
        //删除之前这场考试的统计
        $this->score_total->where('exam_id', $exam_id)->delete();
        //查询参与这场考试的所有班级和科目
        $exam = DB::table('exams')->where('id', $exam_id)->select('class_ids','subject_ids')->first();

        $class = DB::table('classes')
            ->whereIn('id', explode(',', $exam->class_ids))
            ->select('id', 'grade_id')
            ->get();
        //通过年级分组
        $grades = [];
        foreach($class as $item){
            $grades[$item->grade_id][] = $item->id;
        }
        //循环每个年级
        foreach ($grades as $class_ids_arr){
            $data = [];
            //查找此年级参与考试班级的所有学生
            $students = DB::table('students')
                ->whereIn('class_id',$class_ids_arr)
                ->pluck('class_id', 'id');
            //循环学生
            foreach ($students as $student => $class_id){
                //计算总成绩
                $scores = DB::table('scores')
                    ->where(['student_id' => $student, 'exam_id' => $exam_id])
                    ->pluck('score','subject_id');
                $score = 0;
                $subject_ids = '';
                $na_subject_ids = '';
                foreach ( explode(',', $exam->subject_ids) as $v){
                    if(isset($scores[$v]) && $scores[$v] != 0){
                        $subject_ids .= ',' . $v;
                        $score += $scores[$v];
                    }else{
                        $na_subject_ids .= ',' . $v;
                    }
                }
                //建立写入数据库的数组数据
                $insert = [
                    'student_id' => $student,
                    'class_id' => $class_id,
                    'exam_id' => intval($exam_id),
                    'score' => $score,
                    'subject_ids' => empty($subject_ids) ? '' : substr($subject_ids,1),
                    'na_subject_ids' => empty($na_subject_ids) ? '' : substr($na_subject_ids,1)
                ];
                $data []=$insert;
            }

            //根据总成绩排序
            $score_sore = [];
            foreach ($data as $key => $row)
            {
                $score_sore[$key]  = $row['score'];
            }
            array_multisort($score_sore, SORT_DESC, $data);

            //计算年级排名
            $grade_ranks = [];
            foreach ($data as $k => $v){
                $v['grade_rank'] = $k+1;
                if($k>0){
                    if($v['score'] == $data[0]['score']){
                        $v['grade_rank'] = $grade_ranks[0]['grade_rank'];
                    }
                }
                $grade_ranks []= $v;
            }
            
            //通过班级分组
            $classes = [];
            foreach($grade_ranks as $item){
                $classes[$item['class_id']][] = $item;
            }
            //循环每个班级
            foreach ($classes as $v){
                //计算班级排名
                $inserts = [];
                foreach ($v as $c_k => $c_v){
                    $c_v['class_rank'] = $c_k+1;
                    if($c_k>1){
                        if($c_v['score'] == $v[$c_k-1]['score']){
                            $c_v['class_rank'] = $inserts[$c_k-1]['class_rank'];
                        }
                    }
                    unset($c_v['class_id']);
                    $inserts []= $c_v;
                }
                $this->score_total->insert($inserts);
            }
        }
    }

}
