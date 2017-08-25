<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\ScoreRangeRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ScoreRange
 *
 * @property int $id
 * @property string $name 成绩统计项名称
 * @property string $subject_ids 成绩统计项包含的科目IDs
 * @property int $school_id 成绩统计项所属学校ID
 * @property float $start_score 成绩统计项起始分数
 * @property float $end_score 成绩统计项截止分数
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled 是否统计
 * @method static Builder|ScoreRange whereCreatedAt($value)
 * @method static Builder|ScoreRange whereEnabled($value)
 * @method static Builder|ScoreRange whereEndScore($value)
 * @method static Builder|ScoreRange whereId($value)
 * @method static Builder|ScoreRange whereName($value)
 * @method static Builder|ScoreRange whereSchoolId($value)
 * @method static Builder|ScoreRange whereStartScore($value)
 * @method static Builder|ScoreRange whereSubjectIds($value)
 * @method static Builder|ScoreRange whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\School $school
 */
class ScoreRange extends Model {
    //
    protected $table = 'score_ranges';
    protected $fillable = [
        'name',
        'subject_ids',
        'school_id',
        'start_score',
        'end_score',
        'created_at',
        'updated_at',
        'enabled'
    ];
    
    /**
     * 获取拥有该统计项的学校。
     */
    public function school() {
        return $this->belongsTo('App\Models\School');
    }

    public function existed(ScoreRangeRequest $request, $id = NULL) {
        if (!$id) {
            $score_range = $this->where('school_id', $request->input('school_id'))
                ->where('name', $request->input('name'))
                ->where('subject_ids', implode(',', $request->input('subject_ids')))
                ->first();
        } else {
            $score_range = $this->where('school_id', $request->input('school_id'))
                ->where('id', '<>' , $id)
                ->where('name', $request->input('name'))
                ->where('subject_ids', implode(',', $request->input('subject_ids')))
                ->first();
        }
        return $score_range ? true : false;
    }

    public function datatable() {
        $columns = [
            ['db' => 'ScoreRange.id', 'dt' => 0],
            ['db' => 'ScoreRange.name', 'dt' => 1],
            ['db' => 'School.name as schoolname', 'dt' => 2],
            ['db' => 'ScoreRange.start_score', 'dt' => 3],
            ['db' => 'ScoreRange.end_score', 'dt' => 4],
            ['db' => 'ScoreRange.created_at', 'dt' => 5],
            ['db' => 'ScoreRange.updated_at', 'dt' => 6],
            [
                'db' => 'ScoreRange.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'LEFT',
                'conditions' => [
                    'School.id = ScoreRange.school_id'
                ]
            ]
        
        
        ];
        return Datatable::simple($this, $columns, $joins);
    }

    public function statistics($request){
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
        $score_range = $this->select('id', 'name', 'subject_ids', 'start_score', 'end_score')->get()->toArray();
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
