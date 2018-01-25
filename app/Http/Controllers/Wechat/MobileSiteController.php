<?php

namespace App\Http\Controllers\Wechat;


use App\Http\Controllers\Controller;
use App\Models\Corp;
use App\Facades\Wechat;
use App\Models\App;
use App\Models\Department;
use App\Models\Media;
use App\Models\User;
use App\Models\WapSite;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class MobileSiteController extends Controller
{

    /**
     * 微网站首页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wapHome() {

        $corp = new Corp();
        $corps = $corp::whereName('万浪软件')->first();
        $corpId = $corps->corpid;
        $secret = App::whereAgentid('999')->first()->secret;
        $userId = Session::get('userId') ? Session::get('userId') : null;
        
        $code = Request::input('code');

        # 从微信企业号后台获取userid
        if (empty($code)) {
            $codeUrl = Wechat::getCodeUrl($corpId, '999', 'http://weixin.028lk.com/wapsite/home');
            return redirect($codeUrl);
        } elseif(!empty($code) && empty($userId)){
            $accessToken = Wechat::getAccessToken($corpId, $secret);
            $userInfo = json_decode(Wechat::getUserInfo($accessToken, $code), JSON_UNESCAPED_UNICODE);
            $userId = $userInfo['UserId'];
            Session::put('userId',$userId);
        }
        # 获取学校的部门类型
        // $departmentType = new DepartmentType();
        // $type = $departmentType::whereName('学校')->first();
        # 通过微信企业后台返回的userid  获取数据库user数据
        $user = User::where('userid', $userId)->first();
        $department = new Department();
        # 获取当前用户的最高顶级部门
        $level = $department->groupLevel($user->id);
        $group = User::whereId($user->id)->first()->group;
        if ($level == 'school') {
            $school_id = $group->school_id;
            $wapSite = WapSite::
            where('school_id', $school_id)
                ->first();
            if ($wapSite) {
                // dd($wapSite->wapSiteModules->media);
                return view('wechat.wapsite.home', [
                    'wapsite' => $wapSite,
                    // 'code' => $code,
                    'medias'  => Media::medias(explode(',', $wapSite->media_ids)),
                ]);
            }

        }

    }
}
