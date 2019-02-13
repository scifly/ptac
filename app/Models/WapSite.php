<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Snippet};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, DB, Request};
use Illuminate\View\View;
use Throwable;

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
 * @method static Builder|WapSite newModelQuery()
 * @method static Builder|WapSite newQuery()
 * @method static Builder|WapSite query()
 * @mixin Eloquent
 */
class WapSite extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'id', 'school_id', 'site_title',
        'media_ids', 'enabled',
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
            [
                'db' => 'School.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return Snippet::school($d);
                }
            ],
            ['db' => 'WapSite.site_title', 'dt' => 2],
            ['db' => 'WapSite.created_at', 'dt' => 3],
            ['db' => 'WapSite.updated_at', 'dt' => 4],
            [
                'db'        => 'WapSite.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row);
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
        ];
        $ws = $this->where($conditions)->first();
        if (!$ws) {
            $schoolId = $this->schoolId();
            $ws = $this->create([
                'school_id'  => $schoolId,
                'site_title' => School::find($schoolId)->name,
                'media_ids'  => '',
                'enabled'    => Constant::ENABLED,
            ]);
        }
        
        return ['ws' => $ws];
        
    }
    
    /**
     * 上传微网站首页轮播图
     *
     * @return JsonResponse
     */
    function import() {
        
        $files = Request::allFiles();
        $media = new Media();
        $uploadedFiles = [];
        foreach ($files['images'] as $image) {
            abort_if(
                empty($image),
                HttpStatusCode::NOT_ACCEPTABLE,
                __('messages.empty_file')
            );
            $uploadedFile = $media->import(
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
     * @param array $data
     * @param $id
     * @return bool|mixed
     */
    function modify(array $data, $id) {
    
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除微网站
     *
     * @param null $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
    
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $wsmIds = WapSiteModule::whereIn('wap_site_id', $ids)
                    ->pluck('id')->toArray();
                Request::replace(['ids' => $wsmIds]);
                (new WapSiteModule)->remove();
                Request::replace(['ids' => $ids]);
                $this->purge(['WapSite'], 'id');
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
    /**
     * 微信端
     *
     * @return Factory|View|string
     */
    function wIndex() {
        
        $user = Auth::user();
        # 禁止学生访问微网站
        abort_if(
            !$user || $user->role() == '学生',
            HttpStatusCode::UNAUTHORIZED,
            __('messages.unauthorized')
        );
        $wapSite = WapSite::whereSchoolId(session('schoolId'))->first();
        $medias = Media::whereIn('id', explode(',', $wapSite->media_ids))->get();
        abort_if(
            !$wapSite,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return view('wechat.mobile_site.home', [
            'wapsite' => $wapSite,
            'medias'  => $medias,
        ]);
        
    }
    
}
