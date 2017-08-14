<?php

namespace App\Http\Controllers;
use App\Models\PollQuestionnaire;
use App\Models\PollQuestionnaireAnswer;
use App\Models\PollQuestionnaireChoice;
use App\Models\PollQuestionnaireParticipant;
use App\Models\PollQuestionnaireSubject;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class PqParticipantController extends Controller
{
    protected $pollQuestionnaires,$pollQuestionnaireParticipant,
              $pollQuestionnaireAnswer,$pollQuestionnaireChoice,
              $pollQuestionnaireSubject,$user,$school,$tempChoice=[]
              ,$result=[],$count;
    function __construct(PollQuestionnaire $pollQuestionnaires
    ,PollQuestionnaireAnswer $pollQuestionnaireAnswer
    ,PollQuestionnaireChoice $pollQuestionnaireChoice
    ,pollQuestionnaireSubject $pollQuestionnaireSubject
    ,PollQuestionnaireParticipant $pollQuestionnaireParticipant
    ,User $user){
        #投票问卷
        $this->pollQuestionnaires=$pollQuestionnaires;
        #投票问卷参与者
        $this->pollQuestionnaireParticipant=$pollQuestionnaireParticipant;
        #投票问卷列表
        $this->pollQuestionnaireChoice=$pollQuestionnaireChoice;
        #投票问卷选项
        $this->pollQuestionnaireSubject=$pollQuestionnaireSubject;
        #投票问卷参与者
        $this->pollQuestionnaireAnswer=$pollQuestionnaireAnswer;
        #用户
        $this->user=$user;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        #根据登录的角色ID筛选参与的调查问卷
        $result=$this->pollQuestionnaires
               ->join('poll_questionnaire_participants as A','poll_questionnaires.id','A.pq_id')
                #这里获取用户ID
               ->where('A.user_id',1)
               ->get(['poll_questionnaires.id','poll_questionnaires.name']);
        return view('poll_questionnaire_particpation.index',['js'=>'js/poll_questionnaire_particpation/index.js','form'=>0,'pqs'=>$result]);
    }

    /**
     * @param $id
     * @return string
     */
    public function show($id)
    {
        #先获取投票问卷列
        $this->pollQuestionnaireSubject
            ->where('pq_id',$id)
            ->orderBy('id','asc')
            ->each(
                function ($subject){
                    #清空tempchoice
                    unset($this->tempChoice);
                    #获取当前选项题
                    $temp=[];
                    $this->count=0;
                    $subject->pollquestionnairechoice()
                        #对序号排序
                        ->orderBy('seq_no','asc')->each(
                        function ($choice){
                            #用于循环填空
                            $tempC=[];
                            #选项题ID
                            $tempC['id']=$choice->id ;
                            #选项题内容
                            $tempC['choice']=$choice->choice ;
                            #选项题排列序号
                            $tempC['seq_no']=$choice->seq_no ;
                            #数据
                            #获取选项答案
                            #如果有答案
                            if($choice
                                ->pollquestionnaireSubject
                                ->pollquestionnaireAnswer)
                            {
                                $answer = explode(',', $choice
                                    ->pollquestionnaireSubject
                                    ->pollquestionnaireAnswer->answer);
                                #如果是填空
                                if ($choice->pollquestionnaireSubject
                                        ->subject_type == 2) {
                                    #如果有空数据处理
                                    if(count($answer)>$this->count)
                                        $tempC["answer"] = $answer[$this->count];
                                    else
                                        $tempC["answer"] = '';
                                    $this->count++;
                                } else {
                                    #如果答案中存在此问卷项
                                    if (in_array($choice->id, $answer))
                                        $tempC["answer"] = "checked";
                                    else $tempC["answer"] = '';
                                }
                            }
                            else{
                                $tempC["answer"] = '';}
                            $this->tempChoice[] = $tempC;
                        }
                    );
                    #选项ID
                    $temp['id']=$subject->id;
                    #选项关联ID
                    $temp['pq_id']=$subject->pq_id;
                    #选项名称
                    $temp['subject']=$subject->subject;
                    #选项类型0-单选,1-多选,2-填空
                    $temp['subject_type']=$subject->subject_type;
                    #选项题
                    $temp['choices']=$this->tempChoice;

                    $this->result[]=$temp;
                }
            );
       return json_encode($this->result);
        #获取投票问卷下选项
    }
    public function update(Request $q)
    {
        #先获取项和题转换数组操作
       $json=json_decode($this->show($q->get('pollQuestion')));

       foreach ($json as $item)
       {
           $var='';
           switch ($item->subject_type)
           {
               #单选
               case 0:
                   #判断是否为数组
                    if(is_array(Input::get('rd_' . $item->id)))
                        $var= implode(',',Input::get('rd_' . $item->id));
                    else
                        $var=Input::get('rd_' . $item->id);
                   break;
               #多选
               case 1:
                   if(is_array(Input::get('ck_' . $item->id)))
                       $var=implode(',',Input::get('ck_' . $item->id));
                   else
                       $var=Input::get('ck_' . $item->id);
                   break;
               #填空
               case 2:
                   if(is_array(Input::get('text_' . $item->id)))
                       $var= implode(',',Input::get('text_' . $item->id))  ;
                   else
                       $var=Input::get('text_' . $item->id);
                   break;
           }
           #存储答案
           $Answer=$this->pollQuestionnaireAnswer
               ->where('pqs_id',$item->id)->first();
           #判断是否存在，如果存在
           $hasObject=true;
           if(!isset($Answer))
             $hasObject=false;
           #如果不存在创建新Model
           if(!$hasObject) $Answer=new PollQuestionnaireAnswer();

           $Answer->pq_id=$item->pq_id;
           $Answer->pqs_id=$item->id;
           #这里获取Session用户ID
           $Answer->user_id=1;
           $Answer->answer=$var;
           if(!$hasObject)  $Answer->save();
           else $Answer->update();
       }
       return response()->json(['msg'=>'提交成功',''=>self::HTTP_STATUSCODE_OK]);


    }
}
