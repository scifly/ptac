<?php
namespace App\Models;

use Eloquent;
use Throwable;
use Exception;
use Carbon\Carbon;
use Illuminate\View\View;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Helpers\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\WapSiteRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\WapSite 微网站
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property string $site_title 首页抬头
 * @property string $media_ids 首页幻灯片图片多媒体ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read School $school
 * @property-read Collection|\App\Models\WapSiteModule[] $wapSiteModules
 * @method static Builder|WapSite whereCreatedAt($value)
 * @method static Builder|WapSite whereEnabled($value)
 * @method static Builder|WapSite whereId($value)
 * @method static Builder|WapSite whereMediaIds($value)
 * @method static Builder|WapSite whereSchoolId($value)
 * @method static Builder|WapSite whereSiteTitle($value)
 * @method static Builder|WapSite whereUpdatedAt($value)
 * @mixin Eloquent
 */
class WapSite extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'id', 'school_id', 'site_title',
        'media_ids', 'created_at', 'updated_at',
        'enabled',
    ];
    
    /**
     * 获取微网站包含的所有网站模块对象
     *
     * @return HasMany
     */
    function wapSiteModules() { return $this->hasMany('App\Models\WapSiteModule'); }
    
    /**
     * 返回微网站所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 微网站列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'WapSite.id', 'dt' => 0],
            ['db' => 'School.name', 'dt' => 1],
            ['db' => 'WapSite.site_title', 'dt' => 2],
            ['db' => 'WapSite.created_at', 'dt' => 3],
            ['db' => 'WapSite.updated_at', 'dt' => 4],
            [
                'db'        => 'WapSite.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = WapSite.school_id',
                ],
            ],
        ];
        $condition = 'WapSite.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 返回微网站基本信息
     *
     * @return array
     */
    function index() {
        
        $conditions = [
            'school_id' => $this->schoolId(),
            'enabled'   => Constant::ENABLED,
        ];
        $ws = $this->where($conditions)->first();
        if (!$ws) {
            $schoolId = $this->schoolId();
            $ws = $this->create([
                'school_id'  => $schoolId,
                'site_title' => School::find($schoolId)->name,
                'media_ids'  => '',
                'enabled'    => Constant::DISABLED,
            ]);
        }
        
        return [
            'ws'     => $ws,
            'medias' => (new Media())->medias(
                explode(",", $ws->media_ids)
            ),
            'show'   => true,
        ];
        
    }
    
    /**
     * 上传微网站首页轮播图
     *
     * @return JsonResponse
     */
    function upload() {
        
        $files = Request::allFiles();
        $media = new Media();
        $uploadedFiles = [];
        foreach ($files['images'] as $image) {
            abort_if(
                empty($image),
                HttpStatusCode::NOT_ACCEPTABLE,
                __('messages.empty_file')
            );
            $uploadedFile = $media->upload(
                $image, __('messages.wap_site.title')
            );
            abort_if(
                !$uploadedFile,
                HttpStatusCode::INTERNAL_SERVER_ERROR,
                __('messages.file_upload_failed')
            );
            $uploadedFiles[] = $uploadedFile;
        }
        
        return response()->json($uploadedFiles);
        
    }
    
    /**
     * 更新微网站
     *
     * @param WapSiteRequest $request
     * @param $id
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function modify(WapSiteRequest $request, $id) {
        
        $wapSite = self::find($id);
        if (!$wapSite) {
            return false;
        }
        try {
            DB::transaction(function () use ($request, $id) {
                self::removeMedias($request);
                
                return self::find($id)->update(
                    $request->except('_method', '_token', 'del_ids')
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除微网站
     *
     * @param null $id
     * @return bool
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 从微网站中删除指定的媒体数据
     *
     * @param $mediaId
     * @throws Exception
     */
    function removeMedia($mediaId) {
        
        try {
            DB::transaction(function () use ($mediaId) {
                $wapSites = $this->whereRaw($mediaId . ' IN (media_ids)')->get();
                foreach ($wapSites as $wapSite) {
                    $media_ids = implode(
                        ',', array_diff(explode(',', $wapSite->media_ids), [$mediaId])
                    );
                    $wapSite->update(['media_ids' => $media_ids]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 删除指定微网站的所有数据
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->delRelated('wap_site_id', 'WapSiteModule', $id);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回微官网首页
     *
     * @return Factory|View|string
     */
    function wIndex() {
        
        $user = Auth::user();
        # 禁止学生访问微网站
        abort_if(
            !$user || $user->group->name == '学生',
            HttpStatusCode::UNAUTHORIZED,
            __('messages.unauthorized')
        );
        $wapSite = WapSite::whereSchoolId(session('schoolId'))->first();
        abort_if(
            !$wapSite,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return view('wechat.wapsite.home', [
            'wapsite' => $wapSite,
            'medias'  => (new Media())->medias(
                explode(',', $wapSite->media_ids)
            ),
        ]);
        
    }
    
    /**
     * @param $request
     * @throws Exception
     */
    private function removeMedias(WapSiteRequest $request) {
        
        //删除原有的图片
        $mediaIds = $request->input('del_ids');
        if ($mediaIds) {
            $medias = Media::whereIn('id', $mediaIds)->get(['id', 'path']);
            foreach ($medias as $media) {
                Storage::disk('uploads')->delete($media->path);
            }
            Media::whereIn('id', $mediaIds)->delete();
        }
        
    }
    
}
