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
        if(!empty($row) ){

            return $this->fail('该学校已存在微网站！');
        }else{
            //删除原有的图片
            $del_ids = $request->input('del_ids');
            if($del_ids){
                $medias = Media::whereIn('id',$del_ids)->get(['id','path']);

                foreach ($medias as $v)
                {
                    $path_arr = explode("/",$v->path);
                    Storage::disk('uploads')->delete($path_arr[5]);

                }
                $delStatus = Media::whereIn('id',$del_ids)->delete();
            }
            return $this->wapSite->create($data) ? $this->succeed() : $this->fail();
        }

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

        $wapsite = $this->wapSite->find($id);
        if (!$wapsite) { return parent::notFound(); }
        $f = explode(",", $wapsite->media_ids);

        $medias = Media::whereIn('id',$f)->get(['id','path']);

        return parent::output(__METHOD__, [
            'wapsite' => $wapsite,
            'medias' => $medias,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param WapSite $wapSite
     */
    public function edit( $id)
    {
        $wapsite = $this->wapSite->find($id);

        if (!$wapsite) { return parent::notFound(); }
        $f = explode(",", $wapsite->media_ids);

        $medias = Media::whereIn('id',$f)->get(['id','path']);

        return parent::output(__METHOD__, [
            'wapsite' => $wapsite,
            'medias' => $medias,
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
        if (!$data) { return parent::notFound(); }

        $media_ids = $siteRequest->input('media_ids');

        $data->school_id = $siteRequest->input('school_id');
        $data->site_title = $siteRequest->input('site_title');
        $data->enabled = $siteRequest->input('enabled');
        $data->media_ids = implode(',', $media_ids);

        $row = $this->wapSite->where([
            'school_id' => $data->school_id,
        ])->first();
        if(!empty($row) && $row->id != $id){

            return $this->fail('所属学校重复！');
        }else{
            //删除原有的图片
            $del_ids = $siteRequest->input('del_ids');
            if($del_ids){
                $medias = Media::whereIn('id',$del_ids)->get(['id','path']);

                foreach ($medias as $v)
                {
                    $path_arr = explode("/",$v->path);
                    Storage::disk('uploads')->delete($path_arr[5]);

                }
                $delStatus = Media::whereIn('id',$del_ids)->delete();
            }

            return $data->save() ? $this->succeed() : $this->fail();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param WapSite $wapSite
     */
    public function destroy($id)
    {
        $wapsite = $this->wapSite->find($id);

        if (!$wapsite) { return parent::notFound(); }
        return $wapsite->delete() ? parent::succeed() : parent::fail();
    }

    /**
     * @param Request $request
     */
    public function uploadImages(){

//        if (Request::isMethod('post')) {

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
//        }

    }


    public function webindex(){

//        $school_id = isset($_GET['school_id']) ? $_GET['school_id'] : '';

        $wapsite = $this->wapSite->whereId(3)->first();

        $f = explode(",", $wapsite['media_ids']);

        $medias = Media::whereIn('id',$f)->get(['id','path']);

        return view('frontend.wap_site.index', [
            'wapsite' => $wapsite,
            'medias' => $medias,
            'ws' =>true
        ]);

    }
}

