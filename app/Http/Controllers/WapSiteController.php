<?php

namespace App\Http\Controllers;

use App\Http\Requests\WapSiteRequest;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\Media;
use App\Models\WapSite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
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
        $res = $this->wapSite->save($request->all());

        $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
        $this->result['message'] = self::MSG_CREATE_OK;

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
    public function destroy(WapSite $wapSite)
    {
        //
    }
}
