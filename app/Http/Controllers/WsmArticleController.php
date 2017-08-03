<?php

namespace App\Http\Controllers;

use App\Models\WsmArticle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
class WsmArticleController extends Controller
{
    protected $article;

    public function __construct(WsmArticle $article)
    {
        $this->article = $article;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->article->datatable());
        }
        return view('wsm_aritcle.index' , [
            'js' => 'js/wsm_aritcle/index.js',
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
        return view('wsm_aritcle.create',[
            'js' => 'js/wsm_aritcle/create.js',
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
     * @param  \App\Models\WsmArticle  $wsmArticle
     * @return \Illuminate\Http\Response
     */
    public function show(WsmArticle $wsmArticle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WsmArticle  $wsmArticle
     * @return \Illuminate\Http\Response
     */
    public function edit(WsmArticle $wsmArticle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WsmArticle  $wsmArticle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WsmArticle $wsmArticle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WsmArticle  $wsmArticle
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->article->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }
}
