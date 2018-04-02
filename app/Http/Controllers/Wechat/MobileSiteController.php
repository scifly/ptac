<?php
namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;
use App\Helpers\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\Corp;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Group;
use App\Models\Media;
use App\Models\School;
use App\Models\User;
use App\Models\WapSite;
use App\Models\WapSiteModule;
use App\Models\WsmArticle;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class MobileSiteController extends Controller {
    
    protected $media, $department;
    
    function __construct(Media $media, Department $department) {
        
        $this->media = $media;
        $this->department = $department;
        
    }
    
    /**
     * 微网站首页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wapHome() {
        
        $corp = new Corp();
        $corps = $corp::whereName('万浪软件')->first();
        $corpId = $corps->corpid;
        $secret = App::whereAgentid('1000006')->first()->secret;
        $userId = Session::get('userId') ? Session::get('userId') : null;
        $code = Request::input('code');
        if (empty($code) && empty($userId)) {
            $codeUrl = Wechat::getCodeUrl($corpId, '1000006', 'http://weixin.028lk.com/wapsite/home');
            
            return redirect($codeUrl);
        } elseif (!empty($code) && empty($userId)) {
            $accessToken = Wechat::getAccessToken($corpId, $secret);
            $userInfo = json_decode(Wechat::getUserInfo($accessToken, $code), JSON_UNESCAPED_UNICODE);
            $userId = $userInfo['UserId'];
            Session::put('userId', $userId);
        }
        # 通过微信企业后台返回的userid  获取数据库user数据
        $user = User::where('userid', $userId)->first();
        if ($user) {
            if ($user->group_id != 1 && $user->group_id != 2) {
                $school_id = Group::whereId($user->group_id)->first()->school_id;
                if (!$school_id) {
                    $dept_id = DepartmentUser::whereUserId($user->id)->first()->department_id;
                    $schoolDept = $this->department->schoolDeptId($dept_id);
                    $school_id = School::whereDepartmentId($schoolDept)->first()->id;
                }
                $wapSite = WapSite::
                where('school_id', $school_id)
                    ->first();
                if ($wapSite) {
                    return view('wechat.wapsite.home', [
                        'wapsite' => $wapSite,
                        'medias'  => $this->media->medias(explode(',', $wapSite->media_ids)),
                    ]);
                }
            }
        }
        
        return abort(HttpStatusCode::BAD_REQUEST, '请求无效');
        
    }
    
    /**
     * 微网站栏目首页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wapSiteModuleHome() {
        $id = Request::input('id');
        $articles = WsmArticle::whereWsmId($id)
            ->orderByDesc("created_at")->get();
        $module = WapSiteModule::whereId($id)->first();
        
        return view('wechat.wapsite.module_index', [
            'articles' => $articles,
            'module'   => $module,
            'ws'       => true,
        ]);
    }
    
    /**
     * 微网站文章详情
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function articleHome() {
        $id = Request::input('id');
        $article = WsmArticle::find($id);
        
        return view('wechat.wapsite.article', [
            'article' => $article,
            'medias'  => $this->media->medias(explode(',', $article->media_ids)),
        ]);
        
    }
}
