<?php

namespace App\Http\Controllers;

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WapSite  $wapSite
     * @return \Illuminate\Http\Response
     */
    public function show(WapSite $wapSite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WapSite  $wapSite
     * @return \Illuminate\Http\Response
     */
    public function edit(WapSite $wapSite)
    {
        //
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
