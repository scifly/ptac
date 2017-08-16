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

        $media_ids = $request->input('media_ids');
        $data = [
            'school_id' => $request->input('school_id'),
            'site_title' => $request->input('site_title'),
            'media_ids' => implode(',', $media_ids),
            'enabled' => $request->input('enabled')
        ];

        $row = $this->wapSite->where([
                'school_id' => $data['school_id']
            ])->first();
        if(!empty($row)){
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '该学校已存在微网站！';
        }else{
            if($this->wapSite->create($data))
            {
                $this->result['message'] = self::MSG_CREATE_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '';
            }
        }
        return response()->json($this->result);
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
    public function edit( $id)
    {
        $wapsite = $this->wapSite->whereId($id)->first();

        $f = explode(",", $wapsite->media_ids);

        $medias = Media::whereIn('id',$f)->get(['id','path']);

        return view('wap_site.edit', [
            'js' => 'js/wap_site/edit.js',
            'wapsite' => $wapsite,
            'medias' => $medias,
            'form' => true

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param WapSiteRequest $siteRequest
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param WapSite $wapSite
     */
    public function update( WapSiteRequest $siteRequest, $id)
    {
        $data = WapSite::find($id);
        $media_ids = $siteRequest->input('media_ids');

        $data->school_id = $siteRequest->input('school_id');
        $data->site_title = $siteRequest->input('site_title');
        $data->enabled = $siteRequest->input('enabled');

        $row = $this->wapSite->where([
            'school_id' => $data->school_id,
        ])->first();


        if(!empty($row) && $row->id != $id){

            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '所属学校重复！';

        }else{
            //删除原有的图片
            $f = explode(",", $data->media_ids);
            $delStatus = Media::whereIn('id',$f)->delete();

            $data->media_ids = implode(',', $media_ids);

            if($data->save())
            {
                $this->result['message'] = self::MSG_EDIT_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '';

            }
        }
        return response()->json($this->result);
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

    /**
     * @param Request $request
     */
    public function uploadImages(){

        if (Request::isMethod('post')) {

            $files = Request::file('img');

            if (empty($files)){
                $result['statusCode'] = 0;
                $result['message'] = '您还未选择图片！';
                return $result;
            }else{
            $result['data']=array();
            $mes = [];

                foreach ($files  as $key=>$v){

                if ($v->isValid()) {
                    // 获取文件相关信息
                    $originalName = $v->getClientOriginalName(); // 文件原名
                    $ext = $v->getClientOriginalExtension();     // 扩展名//
                    $realPath = $v->getRealPath();   //临时文件的绝对路径
                    $type = $v->getClientMimeType();     // image/jpeg/
//                    dd($originalName,$ext,$realPath);die;

                    // 上传图片
                    $filename =  uniqid() . '.' . $ext;
                    // 使用我们新建的uploads本地存储空间（目录）
                    $init=0;
                    $bool = Storage::disk('uploads')->put($filename,file_get_contents($realPath));

                    $filePath = '/storage/app/uploads/'.date('Y-m-d').'/'.$filename;
                    $data = [
                        'path' => $filePath,
                        'remark' => '微网站轮播图',
                        'media_type_id' => '1',
                        'enabled' => '1',
                    ];
                    $mediaId = Media::insertGetId($data);
                    $mes [] = [
                        'id' => $mediaId,
                        'path' => $filePath,
                    ];
                }
            }
            $result['statusCode'] = 1;
            $result['message'] = '上传成功！';
            $result['data'] = $mes;
            }
            return response()->json($result);
        }

    }


    public function webindex(){

        $school_id = isset($_GET['school_id']) ? $_GET['school_id'] : '';

        $wapsite = WapSite::whereId(1)->first();
        $f = explode(",", $wapsite->media_ids);

        $medias = Media::whereIn('id',$f)->get(['id','path']);
//        foreach ($wapsite->wapsitemodule as $v){
//            dd($v->media);
//
//        }
//        die;
        return view('wap_site.web_index', [
            'wapsite' => $wapsite,
            'medias' => $medias,
            'ws' =>true
        ]);

    }
}

