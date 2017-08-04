<?php

namespace App\Http\Controllers;

use App\Http\Requests\WapSiteRequest;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\Media;
use App\Models\WapSite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class WapSiteController extends Controller
{
    protected $wapSite;

    public function __construct(WapSite $wapSite)
    {
        $this->wapSite = $wapSite;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->wapSite->datatable());
        }
        return view('wap_site.index' , [
            'js' => 'js/wap_site/index.js',
            'dialog' => true,
            'datatable' => true,
            'form' => true,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('wap_site.create',[
            'js' => 'js/wap_site/create.js',
            'form' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WapSiteRequest $request)
    {
        dd($request->all());die;
//        $res = $this->wapSite->save($request->all());
//
//        $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
//        $this->result['message'] = self::MSG_CREATE_OK;
//
//        return response()->json($this->result);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param WapSite $wapSite
     */
    public function show($id)
    {

        $wapsite = WapSite::whereId($id)->first();
        $f = explode(",", $wapsite->media_ids);

        $medias = Media::whereIn('id',$f)->get(['id','path']);

        return view('wap_site.show', [
            'wapsite' => $wapsite,
            'medias' => $medias,
            'ws' =>true
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WapSite  $wapSite
     * @return \Illuminate\Http\Response
     */
    public function edit(WapSite $wapSite)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WapSite  $wapSite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WapSite $wapSite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WapSite  $wapSite
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->wapSite->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }

    public function uploadImage(Request $request){
        $result = Array(
            'success'   =>0,
            'message'   =>'',
        );
        if ($request->isMethod('post')) {
            $file = $request->file('media_ids');
            if (empty($file)){
                $result['success'] = 0;
                $result['message'] = '您还未选择图片！';
                return $result;
            }
            $image=array();
            foreach ($file as $key=>$value)
            {
                $image[]=$key;
            }
            $allowed_extensions = ["png", "jpg", "gif"];
            if ($file->isValid()) {
                if ($file->getClientOriginalExtension() && !in_array($file->getClientOriginalExtension(), $allowed_extensions)) {
                    return ['error' => 'You may only upload png, jpg or gif.'];
                    $result['success'] = 0;
                    $result['message'] = '只能选择[png, jpg ,gif]类型图片！';
                    return $result;
                }

                // 获取文件相关信息
                $originalName = $file->getClientOriginalName(); // 文件原名
                $ext = $file->getClientOriginalExtension();     // 扩展名//
                $realPath = $file->getRealPath();   //临时文件的绝对路径
                $type = $file->getClientMimeType();     // image/jpeg/
                // 上传图片
                $filename = date('Y-m-d-H-i-s') . '-' . uniqid() . '.' . $ext;
                // 使用我们新建的uploads本地存储空间（目录）
                $init=0;
                $bool = Storage::disk('uploads')->put($filename,file_get_contents($realPath));
                $filePath = storage_path('app/uploads/').$filename;

            }
        }

    }
}

