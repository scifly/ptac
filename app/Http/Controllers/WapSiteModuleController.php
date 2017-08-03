<?php

namespace App\Http\Controllers;

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WapSiteModule  $wapSiteModule
     * @return \Illuminate\Http\Response
     */
    public function show(WapSiteModule $wapSiteModule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WapSiteModule  $wapSiteModule
     * @return \Illuminate\Http\Response
     */
    public function edit(WapSiteModule $wapSiteModule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WapSiteModule  $wapSiteModule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WapSiteModule $wapSiteModule)
    {
        //
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
