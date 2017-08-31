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
    protected $media;

    public function __construct(WsmArticle $article, Media $media)
    {
        $this->article = $article;
        $this->media = $media;
    }

    /**
     * 显示微网站文章列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->article->datatable());
        }
        return $this->output(__METHOD__);
    }

    /**
     * 显示创建微网站文章记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create()
    {
        return $this->output(__METHOD__);

    }

    /**
     * 保存新创建的微网站文章记录
     *
     * @param WsmArticleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(WsmArticleRequest $request)
    {
        return $this->article->store($request) ? $this->succeed() : $this->fail();
    }

    /**
     * 显示指定的微网站文章记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $article = $this->article->find($id);
        if (!$article) { return parent::notFound(); }

        return parent::output(__METHOD__, [
            'article' => $article,
            'medias' => $this->media->medias($article->media_ids),
//            'thumbnailMedia' => $this->media->find($article->thumbnail_media_id),
        ]);

    }

    /**
     * 显示编辑指定微网站文章记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $article = $this->article->find($id);
        if (!$article) { return parent::notFound(); }
        $mediaIds = explode(",", $article->media_ids);

        return parent::output(__METHOD__, [
            'article' => $article,
            'medias' => $this->media->medias($mediaIds),
//            'thumbnailMedia' => $this->media->find($article->thumbnail_media_id),
        ]);

    }

    /**
     * 更新指定的微网站文章记录
     *
     * @param WsmArticleRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(WsmArticleRequest $request, $id)
    {
        return $this->article->modify($request, $id) ? $this->succeed() : $this->fail();
    }

    /**
     * 删除指定的微网站文章记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $article = $this->article->find($id);
        if (!$article) { return parent::notFound(); }
        return $article->delete() ? parent::succeed() : parent::fail();
    }

    /**
     * 显示微网站文章详情
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail($id)
    {
        $article = $this->article->find($id);

        return view('frontend.wap_site.article', [
            'article' => $article,
            'medias' => $this->media->medias($article->media_ids),
        ]);
    }
}
