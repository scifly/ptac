<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

/**
 * App\Models\Module - 应用模块
 *
 * @property int $id
 * @property string $name
 * @property string $remark
 * @property int|null $tab_id
 * @property int $school_id 应用模块所属的学校id
 * @property int|null $order 模块位置
 * @property int $media_id 模块图标媒体id
 * @property string|null $uri
 * @property int $isfree 是否为免费模块
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Media $media
 * @property-read School $school
 * @property-read Tab|null $tab
 * @method static Builder|Module whereCreatedAt($value)
 * @method static Builder|Module whereEnabled($value)
 * @method static Builder|Module whereId($value)
 * @method static Builder|Module whereIsfree($value)
 * @method static Builder|Module whereMediaId($value)
 * @method static Builder|Module whereName($value)
 * @method static Builder|Module whereOrder($value)
 * @method static Builder|Module whereRemark($value)
 * @method static Builder|Module whereSchoolId($value)
 * @method static Builder|Module whereTabId($value)
 * @method static Builder|Module whereUpdatedAt($value)
 * @method static Builder|Module whereUri($value)
 * @mixin \Eloquent
 */
class Module extends Model {

    use ModelTrait;
    
    protected $fillable = [
        'name', 'remark', 'tab_id',
        'media_id', 'uri', 'isfree',
        'school_id', 'order', 'enabled'
    ];
    
    /**
     * 返回模块所属的控制器对象
     *
     * @return BelongsTo
     */
    function tab() { return $this->belongsTo('App\Models\Tab'); }
    
    /**
     * 返回模块所属的媒体对象
     *
     * @return BelongsTo
     */
    function media() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 应用模块列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Module.id', 'dt' => 0],
            ['db' => 'Module.name', 'dt' => 1],
            [
                'db' => 'Module.school_id', 'dt' => 2,
                'formatter' => function ($d) {
                    return Snippet::school(School::find($d)->name);
                }
            ],
            [
                'db' => 'Module.tab_id', 'dt' => 3,
                'formatter' => function ($d) {
                    return $d ? Tab::find($d)->comment : '-';
                }
            ],
            [
                'db' => 'Module.uri', 'dt' => 4,
                'formatter' => function ($d) {
                    return $d ?? '-';
                }
            ],
            [
                'db' => 'Media.path', 'dt' => 5,
                'formatter' => function ($d) {
                    return $d ? config('app.url') . Snippet::avatar($d) : '-';
                }
            ],
            [
                'db' => 'Module.isfree', 'dt' => 6,
                'formatter' => function ($d) {
                    return $d ? '增值' : '基本';
                }
            ],
            ['db' => 'Module.created_at', 'dt' => 7, 'dr' => true],
            ['db' => 'Module.updated_at', 'dt' => 8, 'dr' => true],
            [
                'db' => 'Module.enabled', 'dt' => 9,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'medias',
                'alias' => 'Media',
                'type' => 'INNER',
                'conditions' => [
                    'Media.id = Module.media_id'
                ]
            ]
        ];
        $condition = 'Module.school_id IN (' . implode(',', $this->schoolIds()) . ')';
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存应用模块
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新应用模块
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id) {
        
        return $this->find($id)->delete();
        
    }
    
    /**
     * 上传应用模块图标
     *
     * @return JsonResponse
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
    
}
