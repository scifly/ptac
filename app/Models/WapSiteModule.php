<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Http\Requests\WapSiteModuleRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use ReflectionException;
use Throwable;


/**
 * App\Models\WapSiteModule 微网站栏目
 *
 * @property int $id
 * @property int $wap_site_id 所属微网站ID
 * @property string $name 模块名称
 * @property int $media_id 模块图片多媒体ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Media $media
 * @property-read WapSite $wapsite
 * @property-read Collection|WsmArticle[] $wsmArticles
 * @method static Builder|WapSiteModule whereCreatedAt($value)
 * @method static Builder|WapSiteModule whereEnabled($value)
 * @method static Builder|WapSiteModule whereId($value)
 * @method static Builder|WapSiteModule whereMediaId($value)
 * @method static Builder|WapSiteModule whereName($value)
 * @method static Builder|WapSiteModule whereUpdatedAt($value)
 * @method static Builder|WapSiteModule whereWapSiteId($value)
 * @mixin \Eloquent
 */
class WapSiteModule extends Model {

    use ModelTrait;
    
    protected $table = 'wap_site_modules';

    protected $fillable = [
        'id', 'wap_site_id', 'name',
        'media_id', 'enabled',
    ];
    
    function wsmArticles() {
        
        return $this->hasMany('App\Models\WsmArticle', 'wsm_id', 'id');
        
    }

    function media() { return $this->belongsTo('App\Models\Media'); }

    function wapsite() { return $this->belongsTo('App\Models\WapSite'); }
    
    /**
     * 保存网站栏目
     *
     * @param WapSiteModuleRequest $request
     * @return bool|mixed
     * @throws Exception
     * @throws \Throwable
     */
    function store(WapSiteModuleRequest $request) {
        
        try {
            //删除原有的图片
            DB::transaction(function () use ($request) {
                self::removeMedias($request);
                self::create($request->all());
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;

    }
    
    /**
     * 更新网站栏目
     *
     * @param $request
     * @throws Exception
     */
    private function removeMedias(WapSiteModuleRequest $request) {
        
        //删除原有的图片
        $mediaIds = $request->input('del_id');
        if ($mediaIds) {
            $medias = Media::whereIn('id', $mediaIds)->get(['id', 'path']);
            foreach ($medias as $media) {
                Storage::disk('uploads')->delete($media->path);
            }
            try {
                Media::whereIn('id', $mediaIds)->delete();
            } catch (Exception $e) {
                throw $e;
            }
        }
        
    }
    
    /**
     * @param WapSiteModuleRequest $request
     * @param $id
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    function modify(WapSiteModuleRequest $request, $id) {
        
        $wapSite = self::find($id);
        if (!$wapSite) { return false; }
        try {
            DB::transaction(function () use ($request, $id) {
                self::removeMedias($request);
                return self::find($id)->update(
                    $request->except('_method', '_token', 'del_id')
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 移除网站栏目
     *
     * @param $id
     * @return bool|null
     * @throws ReflectionException
     * @throws Exception
     */
    function remove($id) {
        
        $wsm = $this->find($id);
        if (!$wsm) { return false; }
        
        return $this->removable($wsm) ? $wsm->delete() : false;
        
    }
    
    /**
     * 上传微网站栏目图片
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function upload() {
        
        $file = Request::file('file');
        abort_if(
            empty($file),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.empty_file')
        );
        $uploadedFile = (new Media())->upload(
            $file, __('messages.wap_site_module.title')
        );
        abort_if(
            !$uploadedFile,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.file_upload_failed')
        );
        
        return response()->json($uploadedFile);
        
    }
    
    /**
     * 返回微网站栏目列表（后台）
     *
     * @return array
     */
    function datatable() {

        $columns = [
            ['db' => 'WapSiteModule.id', 'dt' => 0],
            ['db' => 'WapSiteModule.name', 'dt' => 1],
            ['db' => 'WapSite.site_title', 'dt' => 2],
            ['db' => 'WapSiteModule.created_at', 'dt' => 3],
            ['db' => 'WapSiteModule.updated_at', 'dt' => 4],
            [
                'db' => 'WapSiteModule.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'wap_sites',
                'alias' => 'WapSite',
                'type' => 'INNER',
                'conditions' => [
                    'WapSite.id = WapSiteModule.wap_site_id',
                ],
            ],
        ];
        $condition = 'WapSite.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 返回微网站栏目列表（微信）
     *
     * @return Factory|View
     */
    function wIndex() {
    
        $id = Request::input('id');
        $articles = WsmArticle::whereWsmId($id)->orderByDesc("created_at")->get();
        $module = $this->find($id);
    
        return view('wechat.wapsite.module', [
            'articles' => $articles,
            'module'   => $module,
            'ws'       => true,
        ]);
        
    }
    
}
