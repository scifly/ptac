<?php

namespace App\Http\Controllers;

use App\Http\Requests\WapSiteRequest;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\Media;
use App\Models\WapSite;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class WapSiteController extends Controller {
    protected $wapSite;
    protected $media;

    public function __construct(WapSite $wapSite, Media $media) {
        $this->wapSite = $wapSite;
        $this->media = $media;
    }

    /**
     * 显示微网站列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->wapSite->datatable());
        }
        return $this->output(__METHOD__);
    }

    /**
     * 显示创建微网站记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        return $this->output(__METHOD__);

    }

    /**
     * 保存新创建的微网站记录
     *
     * @param WapSiteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(WapSiteRequest $request) {

        return $this->wapSite->store($request) ? $this->succeed() : $this->fail();

    }


    /**
     * 显示指定的微网站记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $wapsite = $this->wapSite->find($id);
        if (!$wapsite) {
            return parent::notFound();
        }
        return parent::output(__METHOD__, [
            'wapsite' => $wapsite,
            'medias' => $this->media->medias($wapsite->media_ids),
        ]);
    }

    /**
     * 显示编辑指定微网站记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        $wapsite = $this->wapSite->find($id);
        if (!$wapsite) {
            return parent::notFound();
        }
        return parent::output(__METHOD__, [
            'wapsite' => $wapsite,
            'medias' => $this->media->medias($wapsite->media_ids),
        ]);

    }

    /**
     * 更新指定的微网站记录
     *
     * @param WapSiteRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(WapSiteRequest $request, $id) {
        return $this->wapSite->modify($request, $id) ? $this->succeed() : $this->fail();

    }

    /**
     * 删除指定的微网站记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        $wapsite = $this->wapSite->find($id);

        if (!$wapsite) {
            return parent::notFound();
        }
        return $wapsite->delete() ? parent::succeed() : parent::fail();
    }

    /**
     * 上传图片
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImages() {

        $files = Request::file('img');
        if (empty($files)) {
            $result['statusCode'] = 0;
            $result['message'] = '您还未选择图片！';
            return $result;
        } else {
            $result['data'] = array();
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

    /**
     * 打开微网站首页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wapHome() {

        $wapSite = $this->wapSite
            ->where('school_id', Request::get('school_id'))
            ->first();

        return view('frontend.wap_site.index', [
            'wapsite' => $wapSite,
            'medias' => $this->media->medias($wapSite->media_ids),
            'ws' => true
        ]);

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
            // 使用我们新建的uploads本地存储空间（目录）
            if (Storage::disk('uploads')->put($filename, file_get_contents($realPath))) {
                $filePath = '/storage/app/uploads/' . date('Y-m-d') . '/' . $filename;
                $mediaId = Media::insertGetId([
                    'path' => $filePath,
                    'remark' => '微网站轮播图',
                    'media_type_id' => '1',
                    'enabled' => '1',
                ]);
                $filePaths[] = [
                    'id' => $mediaId,
                    'path' => $filePath,
                ];
            }
        }

    }
}

