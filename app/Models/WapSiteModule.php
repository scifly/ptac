<?php
namespace App\Models;

use App\Facades\Datatable;
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
use Illuminate\View\View;
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
    
    /**
     * 返回所属的媒体对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function media() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * 返回所属的微网站对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function wapsite() { return $this->belongsTo('App\Models\WapSite', 'wap_site_id'); }
    
    /**
     * 返回微网站栏目列表（后台）
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'WapSiteModule.id', 'dt' => 0],
            ['db' => 'WapSiteModule.name', 'dt' => 1],
            ['db' => 'WapSite.site_title', 'dt' => 2],
            ['db' => 'WapSiteModule.created_at', 'dt' => 3],
            ['db' => 'WapSiteModule.updated_at', 'dt' => 4],
            [
                'db'        => 'WapSiteModule.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'wap_sites',
                'alias'      => 'WapSite',
                'type'       => 'INNER',
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
                Request::merge(['ids' => $request->input('del_ids')]);
                (new Media)->remove();
                $this->create($request->all());
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * @param WapSiteModuleRequest $request
     * @param $id
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    function modify(WapSiteModuleRequest $request, $id) {
        
        try {
            DB::transaction(function () use ($request, $id) {
                Request::merge(['ids' => $request->input('del_ids')]);
                (new Media)->remove();
                $this->find($id)->update(
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
     * @throws Exception
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定微网站栏目的所有数据
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                WsmArticle::whereWsmId($id)->delete();
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /** 微信端 ------------------------------------------------------------------------------------------------------- */

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
    
    /** Helper functions -------------------------------------------------------------------------------------------- */

    /**
     * 返回微网站栏目列表（微信）
     *
     * @return Factory|View
     */
    function wIndex() {
        
        $id = Request::input('id');
        $articles = WsmArticle::whereWsmId($id)
            ->where('enabled', 1)
            ->orderByDesc("created_at")
            ->get();
        $module = $this->find($id);
        
        return view('wechat.wapsite.module', [
            'articles' => $articles,
            'module'   => $module,
            'ws'       => true,
        ]);
        
    }
    
}
