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

    public function __construct(WapSiteModule $wapSiteModule)
    {
        $this->wapSiteModule = $wapSiteModule;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->wapSiteModule->datatable());
        }
        return $this->output(__METHOD__);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->output(__METHOD__);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param WapSiteModuleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(WapSiteModuleRequest $request)
    {
        // request
        $data = [
            'name' => $request->input('name'),
            'wap_site_id' => $request->input('wap_site_id'),
            'media_id' => $request->input('media_id'),
            'enabled' => $request->input('enabled')
        ];

        $del_id = $request->input('del_id');
        if($del_id){
            $media = Media::whereIn('id',$del_id)->get(['id','path']);

            foreach ($media as $v)
            {
                $path_arr = explode("/",$v->path);
                Storage::disk('uploads')->delete($path_arr[5]);

            }
            $delStatus = Media::whereIn('id',$del_id)->delete();
        }
        return $this->wapSiteModule->create($data) ? $this->succeed() : $this->fail();
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $module = $this->wapSiteModule->find($id);
        if (!$module) { return parent::notFound(); }


        return parent::output(__METHOD__, [
            '$module' => $module,
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $module = $this->wapSiteModule->find($id);

        if (!$module) { return parent::notFound(); }

        return parent::output(__METHOD__, [
            'module' => $module,
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param WapSiteModuleRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(WapSiteModuleRequest $request, $id)
    {
        $data = WapSiteModule::find($id);

        $data->wap_site_id = $request->input('wap_site_id');
        $data->name = $request->input('name');
        $data->media_id = $request->input('media_id');
        $data->enabled = $request->input('enabled');

        //删除原有的图片
        $del_id = $request->input('del_id');
        if($del_id){
            $media = Media::whereIn('id',$del_id)->get(['id','path']);

            foreach ($media as $v)
            {
                $path_arr = explode("/",$v->path);
                Storage::disk('uploads')->delete($path_arr[5]);

            }
            $delStatus = Media::whereIn('id',$del_id)->delete();
        }
        return $data->save() ? $this->succeed() : $this->fail();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WapSiteModule  $wapSiteModule
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $wapsitemodule = $this->wapSiteModule->find($id);

        if (!$wapsitemodule) { return parent::notFound(); }
        return $wapsitemodule->delete() ? parent::succeed() : parent::fail();
    }
    public function webindex($id){


        $articles = WsmArticle::where('wsm_id',$id)->get();
//        foreach ($articles as $v)
//        {
//            dd($v->thumbnailmedia);
//        }die;
        return view('frontend.wap_site.module', [
            'articles' => $articles,
            'ws' =>true
        ]);

    }
}
