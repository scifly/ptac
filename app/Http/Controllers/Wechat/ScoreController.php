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
        switch ($role){
            case '监护人':
                $students = User::whereUserid($userId)->first()->custodian->students;
                $score = $studentName =[];
                foreach ($students as $k=>$s)
                {
                    $exams = Exam::where('class_ids','like','%' . $s->class_id . '%')
                        ->get();
                    foreach ($exams as $e)
                    {
                        $score[$k]['name'] = $e->name;
                        $score[$k]['strat_date'] = $e->start_date;
                    }
                    $score[$k]['user_id'] = $s->user_id;
                    $score[$k]['realname'] = $s->user->realname;
                    $score[$k]['class_id'] = $s->class_id;
                    $studentName[$s->user_id] = $s->user->realname;
                    ksort($studentName);

                }
                return view('wechat.scores.students_score_lists',[
                    'scores' => $score,
                    'studentName' => $studentName,
                ]);
                break;
            case '教职员工':
                break;
            default:
                break;
        }
    }
}