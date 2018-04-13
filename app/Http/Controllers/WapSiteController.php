<?php
namespace App\Http\Controllers;

use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Http\Requests\WapSiteRequest;
use App\Models\ConferenceQueue;
use App\Models\Media;
use App\Models\School;
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
    
    protected $ws, $media, $school;
    
    public function __construct(WapSite $ws, Media $media, School $school) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->ws = $ws;
        $this->media = $media;
        $this->school = $school;
        
    }
    
    /**
     * 微网站列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        $ws = WapSite::whereSchoolId($this->school->schoolId())
            ->where('enabled', Constant::ENABLED)->first();
        if (!$ws) {
            $schoolId = $this->school->schoolId();
            $ws = $this->ws->create([
                'school_id' => $schoolId,
                'site_title' => School::find($schoolId)->name,
                'media_ids' => '',
                'enabled' => Constant::DISABLED
            ]);
        }
        $mediaIds = explode(",", $ws->media_ids);
        
        return $this->output([
            'ws'     => $ws,
            'medias' => $this->media->medias($mediaIds),
            'show'   => true,
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
        
        return $this->result(
            $this->ws->store($request)
        );
        
    }
    
    /**
     * 编辑微网站
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $ws = WapSite::find($id);
        abort_if(!$ws, HttpStatusCode::NOT_FOUND);
        
        return $this->output([
            'ws'     => $ws,
            'medias' => $this->media->medias(explode(',', $ws->media_ids)),
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
        
        $ws = WapSite::find($id);
        abort_if(!$ws, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $ws->modify($request, $id)
        );
        
    }
    
    /**
     * 删除微网站
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $ws = WapSite::find($id);
        abort_if(!$ws, HttpStatusCode::NOT_FOUND);
        
        return $this->result($ws->delete());
        
    }
    
    /**
     * 上传图片
     *
     * @return JsonResponse
     */
    public function uploadImages() {
        
        $files = Request::file('img');
        abort_if(empty($files), HttpStatusCode::NOT_ACCEPTABLE, '您还未选择图片！');
        $this->result['data'] = [];
        $mes = [];
        foreach ($files as $key => $file) {
            $this->validateFile($file, $mes);
        }
        $this->result['message'] = '上传成功！';
        $this->result['data'] = $mes;
        $token = '';
        if ($mes) {
            $path = '';
            foreach ($mes AS $m)
                $path = dirname(public_path()) . '/' . $m['path'];
            $data = ["media" => curl_file_create($path)];
            Wechat::uploadMedia($token, 'image', $data);
        }
        
        return response()->json($this->result);
        
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
    
}

