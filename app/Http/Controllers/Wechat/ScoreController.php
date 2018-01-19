<?php

namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class ScoreController extends Controller{

    public function index()
    {
        // $corpId = 'wxe75227cead6b8aec';
        // $secret = 'uorwAVlN3_EU31CDX0X1oQJk9lB0Or41juMH-cLcIEU';
        // $agentId = 1000007;
        // $userId = Session::get('userId') ? Session::get('userId') : null;
        // $code = Request::input('code');
        // if (empty($code) && empty($userId)) {
        //     $codeUrl = Wechat::getCodeUrl($corpId, $agentId, 'http://weixin.028lk.com/score_lists');
        //     return redirect($codeUrl);
        // }elseif(!empty($code) && empty($userId)){
        //     $accessToken = Wechat::getAccessToken($corpId, $secret);
        //     $userInfo = json_decode(Wechat::getUserInfo($accessToken, $code), JSON_UNESCAPED_UNICODE);
        //     $userId = $userInfo['UserId'];
        //     Session::put('userId',$userId);
        // }
        $userId = 'wangdongxi';
        $role = User::whereUserid($userId)->first()->group->name;
        $pageSize = 4;
        $start = Request::get('start') ? Request::get('start') * $pageSize : 0;
        switch ($role){
            case '监护人':
                if(Request::isMethod('post'))
                {
                    $data = $this->getStudentScore($userId);
                    $score = $data['score'];
                    $scores=array_slice($score[0],$start,$pageSize);
                    return response()->json(['data' => $scores ,'statusCode' => 200 ]);
                }
                $data = $this->getStudentScore($userId);
                $score = $data['score'];
                $studentName = $data['studentName'];
                $scores=array_slice($score[0],$start,$pageSize);
                return view('wechat.scores.students_score_lists',[
                    'scores' => $scores,
                    'studentName' => json_encode($studentName, JSON_UNESCAPED_UNICODE),
                ]);
                break;
            case '教职员工':
                break;
            default:
                break;
        }
    }

    /**
     * @param $userId
     * @return array
     */
    public function getStudentScore( $userId)
    {
        $students = User::whereUserid($userId)->first()->custodian->students;
        $score = $data =[];
        foreach ($students as $k=>$s)
        {
            $exams = Exam::where('class_ids','like','%' . $s->class_id . '%')
                ->get();
            foreach ($exams as $key=>$e)
            {
                $score[$k][$key]['id'] = $e->id;
                $score[$k][$key]['name'] = $e->name;
                $score[$k][$key]['start_date'] = $e->start_date;
                $score[$k][$key]['user_id'] = $s->user_id;
                $score[$k][$key]['realname'] = $s->user->realname;
                $score[$k][$key]['class_id'] = $s->class_id;
            }
            // $studentName['title'] = '选择学生';
            $studentName[]= [
                'title' => $s->user->realname,
                'value' => $s->user_id,
            ];
        }
        $data = [
            'score' => $score,
            'studentName' => $studentName
        ];

        return $data;
    }

}