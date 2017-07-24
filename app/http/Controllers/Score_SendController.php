<?php

namespace App\Http\Controllers;
//用到学校数据模型
use App\Models\School as school;
//用到年级数据模型
use App\Models\Grade as grade;
//班级数据模型
use App\models\Classes as classes;
//考试数据模型
use App\models\Exam as exam;
use Illuminate\Http\Request;
//成绩发送控制
class Score_SendController extends Controller
{
    /**
     * 发送成绩首页,加载学校列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //先通过角色判断管理员、政教、年级主任等 多角色获取
        //如果是普通老师获取关联的考次，班主任获取管理班级所考次，科任老师获取任教科目考次


        $schools=school::all(['name','id']);

        return view("score_send.index",['js' => 'js/score_send/index.js','schools'=>$schools]);
    }

    /**
     *获取年级信息
     */
    public function getGrade($id=null)
    {
        $grade=grade::all(['id','name','school_id'])->where("school_id",$id);

        return json_encode($grade);

    }
    /**
     *获取班级
     */
    public function getClass($id=null)
    {
        $class=classes::all(['id','name','grade_id'])->where("grade_id",$id);

        return json_encode($class);

    }

}
