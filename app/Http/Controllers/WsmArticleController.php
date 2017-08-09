<?php

namespace App\Http\Controllers;

use App\Http\Requests\WsmArticleRequest;
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
        return view('wsm_article.index' , [
            'js' => 'js/wsm_article/index.js',
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
        return view('wsm_article.create',[
            'js' => 'js/wsm_article/create.js',
            'form' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WsmArticleRequest $request)
    {
        // request
        $media_ids = $request->input('media_ids');

        $data = [
            'wsm_id' => $request->input('wsm_id'),
            'name' => $request->input('name'),
            'summary' => $request->input('wap_site_id'),
            'thumbnail_media_id' => $request->input('thumbnail_media_id'),
            'content' => $request->input('content'),
            'media_ids' => $media_ids,
            'enabled' => $request->input('enabled')
        ];

        if($this->article->create($data))
        {
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }

        return response()->json($this->result);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param WsmArticle $wsmArticle
     */
    public function show($id)
    {
        $article = WsmArticle::whereId($id)->first();
        $f = explode(",", $article->media_ids);

        $medias = Media::whereIn('id',$f)->get(['id','path']);

        return view('wsm_article.show', [
            'article' => $article,
            'medias' => $medias,
            'ws' =>true
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param WsmArticle $wsmArticle
     */
    public function edit($id)
    {
        $article = $this->article->whereId($id)->first();

        $f = explode(",", $article->media_ids);

        $medias = Media::whereIn('id',$f)->get(['id','path']);

        return view('wsm_article.edit', [
            'js' => 'js/wsm_article/edit.js',
            'article' => $article,
            'medias' => $medias,
            'form' => true

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WsmArticle  $wsmArticle
     * @return \Illuminate\Http\Response
     */
    public function update(WsmArticleRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param WsmArticle $wsmArticle
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
