<?php

namespace App\Http\Controllers;

use App\Http\Requests\WsmArticleRequest;
use App\Models\Media;
use App\Models\WsmArticle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

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
     * @param WsmArticleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(WsmArticleRequest $request)
    {
        // request
        $media_ids = $request->input('media_ids');

        $data = [
            'wsm_id' => $request->input('wsm_id'),
            'name' => $request->input('name'),
            'summary' => $request->input('summary'),
            'thumbnail_media_id' => $media_ids[0],
            'content' => $request->input('content'),
            'media_ids' => implode(',',$media_ids),
            'enabled' => $request->input('enabled')
        ];
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

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param WsmArticle $wsmArticle
     */
    public function show($id)
    {
        $article = $this->article->find($id);
        if (!$article) { return parent::notFound(); }
        $f = explode(",", $article->media_ids);

        $medias = Media::whereIn('id',$f)->get(['id','path']);
        return parent::output(__METHOD__, [
            'article' => $article,
            'medias' => $medias,
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

        if (!$article) { return parent::notFound(); }
        $f = explode(",", $article->media_ids);

        $medias = Media::whereIn('id',$f)->get(['id','path']);
        return parent::output(__METHOD__, [
            'article' => $article,
            'medias' => $medias,
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param WsmArticleRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(WsmArticleRequest $request, $id)
    {
        $data = WsmArticle::find($id);
        $media_ids = $request->input('media_ids');

        $data->wsm_id = $request->input('wsm_id');
        $data->name = $request->input('name');
        $data->summary = $request->input('summary');
        $data->thumbnail_media_id = $media_ids[0];
        $data->content = $request->input('content');
        $data->media_ids = implode(",", $media_ids);
        $data->enabled = $request->input('enabled');

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

        return $data->save() ? $this->succeed() : $this->fail();
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
        $wsm = $this->article->find($id);

        if (!$wsm) { return parent::notFound(); }
        return $wsm->delete() ? parent::succeed() : parent::fail();
    }

    public function detail($id)
    {
        $article = WsmArticle::whereId($id)->first();
        $f = explode(",", $article->media_ids);

        $medias = Media::whereIn('id',$f)->get(['id','path']);

        return view('frontend.wap_site.article', [
            'article' => $article,
            'medias' => $medias,
            'ws' =>true
        ]);
    }
}
