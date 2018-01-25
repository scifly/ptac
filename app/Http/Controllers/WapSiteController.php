<?php
namespace App\Http\Controllers;

use App\Facades\Wechat;
use App\Http\Requests\WapSiteRequest;
use App\Models\App;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Media;
use App\Models\School;
use App\Models\User;
use App\Models\WapSite;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Support\Facades\Session;

/**
 * 微网站
 *
 * Class WapSiteController
 * @package App\Http\Controllers
 */
class WapSiteController extends Controller {
    
    public function __construct() {
        
        $this->middleware(['auth', 'checkrole']);

    }
    
    /**
     * 微网站列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {

        $schoolId = School::schoolId();
        $wapSite = WapSite::whereSchoolId($schoolId)->where('enabled',1)->first();
        if (empty($wapSite)) {
            return parent::notFound();
        }
        $mediaIds = explode(",", $wapSite->media_ids);
    
        return $this->output([
            'wapSite' => $wapSite,
            'medias'  => Media::medias($mediaIds),
            'show'    => true,
        ]);
    
    }
    
    /**
     * 保存微网站
     *
     * @param WapSiteRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(WapSiteRequest $request) {
        
        return $this->result(WapSite::store($request));

    }
    
    /**
     * 编辑微网站
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {

        $wapSite = WapSite::find($id);
        if (!$wapSite) { return parent::notFound(); }
        
        return $this->output([
            'wapSite' => $wapSite,
            'medias'  => Media::medias(explode(',',$wapSite->media_ids)),
        ]);
        
    }
    
    /**
     * 更新微网站
     *
     * @param WapSiteRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(WapSiteRequest $request, $id) {
        
        return $this->result(WapSite::modify($request, $id));

    }
    
    /**
     * 删除微网站
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $wapsite = WapSite::find($id);
        if (!$wapsite) {
            return parent::notFound();
        }
        
        return $this->result($wapsite->delete());
        
    }
    
    /**
     * 上传图片
     *
     * @return JsonResponse
     */
    public function uploadImages() {
        
        $files = Request::file('img');
        $type = Request::query('type');
        if (empty($files)) {
            $result['statusCode'] = 0;
            $result['message'] = '您还未选择图片！';
            return $result;
        } else {
            $result['data'] = [];
            $mes = [];
            foreach ($files as $key => $file) {
                $this->validateFile($file, $mes);
            }
            $result['statusCode'] = 1;
            $result['message'] = '上传成功！';
            $result['data'] = $mes;
            $token = '';
            if ($mes) {
                $path = '';
                foreach ($mes AS $m)
                    $path = dirname(public_path()) . '/' . $m['path'];
                    $data = ["media" => curl_file_create($path)];

                    Wechat::uploadMedia($token, 'image', $data);
            }

        }
        
        return response()->json($result);
        
    }
    
    /**
     * @param UploadedFile $file
     * @param array $filePaths
     */
    private function validateFile(UploadedFile $file, array &$filePaths) {
        
        if ($file->isValid()) {
            // 获取文件相关信息
            # 文件原名
            $file->getClientOriginalName();
            # 扩展名
            $ext = $file->getClientOriginalExtension();
            # 临时文件的绝对路径
            $realPath = $file->getRealPath();
            # image/jpeg/
            $file->getClientMimeType();
            // 上传图片
            $filename = uniqid() . '.' . $ext;
            // 使用新建的uploads本地存储空间（目录）
            if (Storage::disk('uploads')->put($filename, file_get_contents($realPath))) {
                // $filePath = 'storage/app/uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $filename;
                $filePath = 'uploads/' .
                    date('Y') . '/' .
                    date('m') . '/' .
                    date('d') . '/' .
                    $filename;
                $mediaId = Media::insertGetId([
                    'path'          => $filePath,
                    'remark'        => '微网站轮播图',
                    'media_type_id' => '1',
                    'enabled'       => '1',
                ]);
                $filePaths[] = [
                    'id'   => $mediaId,
                    'path' => $filePath,
                ];
            }
        }
        
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
         $secret = App::whereAgentid('999')->first()->secret;
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

