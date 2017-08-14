<?php

namespace App\Http\Controllers;

use App\Http\Requests\WapSiteModuleRequest;
use App\Http\Requests\WapSiteRequest;
use App\Models\WapSiteModule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

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
        return view('wap_site_module.index' , [
            'js' => 'js/wap_site_module/index.js',
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
        return view('wap_site_module.create',[
            'js' => 'js/wap_site_module/create.js',
            'form' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
//        $row = $this->wapSiteModule->where([
//                'name' => $data['name']
//            ])->first();
//        if(!empty($row)){
//            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
//            $this->result['message'] = '名称重复！';
//        }else{
            if($this->wapSiteModule->create($data))
            {
                $this->result['message'] = self::MSG_CREATE_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '';
            }
//        }

        return response()->json($this->result);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WapSiteModule  $wapSiteModule
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $module = WapSiteModule::whereId($id)->first();

        return view('wap_site_module.show', [
            'module' => $module,
            'ws' =>true
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WapSiteModule  $wapSiteModule
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $module = $this->wapSiteModule->whereId($id)->first();
        return view('wap_site_module.edit', [
            'js' => 'js/wap_site_module/edit.js',
            'module' => $module,
            'form' => true

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WapSiteModule  $wapSiteModule
     * @return \Illuminate\Http\Response
     */
    public function update(WapSiteModuleRequest $request, $id)
    {
        $data = WapSiteModule::find($id);

        $data->wap_site_id = $request->input('wap_site_id');
        $data->name = $request->input('name');
        $data->media_id = $request->input('media_id');
        $data->enabled = $request->input('enabled');

//        $row = $this->wapSiteModule->where([
//            'school_id' => $data->school_id,
//        ])->first();
//        if(!empty($row) && $row->id != $id){
//
//            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
//            $this->result['message'] = '所属学校重复！';
//
//        }else{
            if($data->save())
            {
                $this->result['message'] = self::MSG_EDIT_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '';

            }
//        }
        return response()->json($this->result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WapSiteModule  $wapSiteModule
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->wapSiteModule->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }
}
