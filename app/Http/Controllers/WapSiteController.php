<?php
namespace App\Http\Controllers;

use App\Facades\Wechat;
use App\Http\Requests\WapSiteRequest;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Media;
use App\Models\User;
use App\Models\WapSite;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * 微网站
 *
 * Class WapSiteController
 * @package App\Http\Controllers
 */
class WapSiteController extends Controller {
    
    protected $wapSite;
    protected $media;
    
    public function __construct(WapSite $wapSite, Media $media) {
        
        $this->middleware(['auth']);
        $this->wapSite = $wapSite;
        $this->media = $media;
        
    }
    
    /**
     * 微网站列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->wapSite->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建微网站
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存微网站
     *
     * @param WapSiteRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(WapSiteRequest $request) {
        
        return $this->wapSite->store($request)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 微网站详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $wapsite = $this->wapSite->find($id);
        if (!$wapsite) {
            return parent::notFound();
        }
        $mediaIds = explode(",", $wapsite->media_ids);
        
        return parent::output(__METHOD__, [
            'wapsite' => $wapsite,
            'medias'  => $this->media->medias($mediaIds),
        ]);
        
    }
    
    /**
     * 编辑微网站
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        $wapSite = $this->wapSite->find($id);
        if (!$wapSite) {
            return parent::notFound();
        }
        
        return parent::output(__METHOD__, [
            'wapSite' => $wapSite,
            'medias'  => $this->media->medias($wapSite->media_ids),
        ]);
        
    }
    
    /**
     * 更新微网站
     *
     * @param WapSiteRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(WapSiteRequest $request, $id) {
        
        return $this->wapSite->modify($request, $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除微网站
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $wapsite = $this->wapSite->find($id);
        if (!$wapsite) { return parent::notFound(); }
        
        return $wapsite->delete() ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 上传图片
     *
     * @return JsonResponse
     */
    public function uploadImages() {
        
        $files = Request::file('img');
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
        }
        
        return response()->json($result);
        
    }
    
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
            if (Storage::disk('public')->put($filename, file_get_contents($realPath))) {
                // $filePath = 'storage/app/uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $filename;
                $filePath = Storage::url('public/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $filename);
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
     * @param \Illuminate\Http\Request $request
     * @param $school_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wapHome(\Illuminate\Http\Request $request, $school_id) {
        
        $corp = new Corp();
        $corps = $corp::whereName('万浪软件')->first();
        $corpId = $corps->corpid;
        $secret = $corps->corpsecret;
        $dir = dirname(__FILE__);
        $path = substr($dir, 0, stripos($dir, 'app/Jobs'));
        $tokenFile = $path . 'public/token.txt';
        $token = Wechat::getAccessToken($tokenFile, $corpId, $secret);
        $code = $request->input('code');
        if (empty($code)) {
//            $codeUrl = Wechat::getCodeUrl($corpId, '1000006', 'http://weixin.028lk.com/wap_sites/userInfo');
            $codeUrl = Wechat::getCodeUrl($corpId, '1000006', 'http://weixin.028lk.com/wap_sites/webindex');
            $url = explode('https', $codeUrl);
            
            return redirect('https' . $url[1]);
        } else {
            # 从微信企业号后台获取userid
            $userInfo = Wechat::getUserInfo($token, $code);
            $wechatUserInfo = json_decode($userInfo);
            # 获取学校的部门类型
            // $departmentType = new DepartmentType();
            // $type = $departmentType::whereName('学校')->first();
            # 通过微信企业后台返回的userid  获取数据库user数据
            $user = User::where('userid', $wechatUserInfo['UserId'])->first();
            $department = new Department();
            # 获取当前用户的最高顶级部门
            $level = $department->groupLevel($user->id);
            $group = User::whereId($user->id)->first()->group;
            if ($level == 'school' || $school_id) {
                $school_id = empty($school_id) ? $group->school_id : $school_id;
                $wapSite = $this->wapSite
                    ->where('school_id', $school_id)
                    ->first();
                
                // dd($wapSite->wapSiteModules->media);
                return view('frontend.wap_site.index', [
                    'wapsite' => $wapSite,
                    // 'code' => $code,
                    'medias'  => $this->media->medias($wapSite->media_ids),
                    'ws'      => true,
                ]);
            } else {
            
            }
        }
        
    }
    
}

