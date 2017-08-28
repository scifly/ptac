<?php

namespace App\Http\Controllers;

use App\Http\Requests\WapSiteModuleRequest;
use App\Http\Requests\WapSiteRequest;
use App\Models\Media;
use App\Models\WapSiteModule;
use App\Models\WsmArticle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class WapSiteModuleController extends Controller
{
    protected $wapSiteModule;
    protected $media;

    public function __construct(WapSiteModule $wapSiteModule, Media $media)
    {
        $this->wapSiteModule = $wapSiteModule;
        $this->media = $media;
    }

    /**
     * 显示微网站模块列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->wapSiteModule->datatable());
        }
        return $this->output(__METHOD__);
    }

    /**
     * 显示创建微网站模块记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create()
    {
        return $this->output(__METHOD__);

    }

    /**
     * 保存新创建的微网站模块记录
     *
     * @param WapSiteModuleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(WapSiteModuleRequest $request)
    {

        return $this->wapSiteModule->store($request) ? $this->succeed() : $this->fail();
    }

    /**
     * 显示指定的微网站模块记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $module = $this->wapSiteModule->find($id);
        if (!$module) { return parent::notFound(); }
        return parent::output(__METHOD__, [
            '$module' => $module,
            'media' => $this->media->find($module->media_id),

        ]);

    }

    /**
     * 显示编辑指定微网站模块记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $module = $this->wapSiteModule->find($id);

        if (!$module) { return parent::notFound(); }

        return parent::output(__METHOD__, [
            'module' => $module,
            'media' => $this->media->find($module->media_id),
        ]);

    }

    /**
     * 更新指定的微网站模块记录
     *
     * @param WapSiteModuleRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(WapSiteModuleRequest $request, $id)
    {

        return $this->wapSiteModule->modify($request, $id) ? $this->succeed() : $this->fail();

    }

    /**
     * 删除指定的微网站模块记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $wapSiteModule = $this->wapSiteModule->find($id);

        if (!$wapSiteModule) { return parent::notFound(); }
        return $wapSiteModule->delete() ? parent::succeed() : parent::fail();
    }

    /**
     * 打开微网站模块首页
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wapSiteModuleHome($id)
    {
        $articles = WsmArticle::where('wsm_id',$id)->get();
        return view('frontend.wap_site.module', [
            'articles' => $articles,
            'ws' =>true
        ]);

    }
}
