<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Http\Requests\WapSiteRequest;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
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
                'db' => 'WapSite.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
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
    
        $ws = self::whereSchoolId($this->schoolId())->where('enabled', Constant::ENABLED)->first();
        if (!$ws) {
            $schoolId = $this->schoolId();
            $ws = $this->create([
                'school_id' => $schoolId,
                'site_title' => School::find($schoolId)->name,
                'media_ids' => '',
                'enabled' => Constant::DISABLED
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
     * 上传媒体文件
     *
     * @return JsonResponse
     */
    function upload() {
    
        $files = Request::file('img');
        abort_if(
            empty($files),
            HttpStatusCode::NOT_ACCEPTABLE,
            '您还未选择图片！'
        );
        $result['data'] = [];
        $mes = [];
        foreach ($files as $key => $file) {
            $this->validateFile($file, $mes);
        }
        $result['message'] = '上传成功！';
        $result['data'] = $mes;
        $token = '';
        if ($mes) {
            $path = '';
            foreach ($mes AS $m) {
                $path = dirname(public_path()) . '/' . $m['path'];
            }
            $data = ["media" => curl_file_create($path)];
            Wechat::uploadMedia($token, 'image', $data);
        }
    
        return response()->json($result);
        
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
        if (!$wapSite) { return false; }
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
     * 返回微官网首页
     *
     * @return Factory|View|string
     */
    function wIndex() {
    
        $user = Auth::user();
        $schoolId = Group::find($user->group_id)->school_id;
        if (!$schoolId) {
            # todo 显示微网站列表
            switch ($user->group->name) {
                case '运营':
                case '企业':
                case '监护人':
                default:
                    break;
            }
            return '<h1>学校列表</h1>';
        } else {
            $wapSite = WapSite::whereSchoolId($schoolId)->first();
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
            try {
                Media::whereIn('id', $mediaIds)->delete();
            } catch (Exception $e) {
                throw $e;
            }
        }
        
    }
    
    /**
     * 验证上传文件
     *
     * @param UploadedFile $file
     * @param array $filePaths
     */
    private function validateFile(UploadedFile $file, array &$filePaths) {
    
        if ($file->isValid()) {
            // 获取文件相关信息
            # 文件原名
            $file->getClientOriginalName();
            # 扩展名
            $ext = $file->getClientOriginalExtension();
            # 临时文件的绝对路径
            $realPath = $file->getRealPath();
            # image/jpeg/
            $file->getClientMimeType();
            // 上传图片
            $filename = uniqid() . '.' . $ext;
            // 使用新建的uploads本地存储空间（目录）
            if (Storage::disk('uploads')->put($filename, file_get_contents($realPath))) {
                // $filePath = 'storage/app/uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $filename;
                $filePath = 'uploads/' .
                    date('Y') . '/' .
                    date('m') . '/' .
                    date('d') . '/' .
                    $filename;
                $mediaId = Media::insertGetId([
                    'path'          => $filePath,
                    'remark'        => '微网站轮播图',
                    'media_type_id' => '1',
                    'enabled'       => '1',
                ]);
                $filePaths[] = [
                    'id'   => $mediaId,
                    'path' => $filePath,
                ];
            }
        }
    }
    
}
