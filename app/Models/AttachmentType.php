<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\HasMany};
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\AttachmentType 附件类型
 *
 * @property int $id
 * @property string $name 附件类型名称
 * @property string $remark 附件类型备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Attachment[] $attachments
 * @method static Builder|AttachmentType whereCreatedAt($value)
 * @method static Builder|AttachmentType whereEnabled($value)
 * @method static Builder|AttachmentType whereId($value)
 * @method static Builder|AttachmentType whereName($value)
 * @method static Builder|AttachmentType whereRemark($value)
 * @method static Builder|AttachmentType whereUpdatedAt($value)
 * @method static Builder|AttachmentType newModelQuery()
 * @method static Builder|AttachmentType newQuery()
 * @method static Builder|AttachmentType query()
 * @mixin Eloquent
 * @property-read int|null $attachments_count
 */
class AttachmentType extends Model {
    
    use ModelTrait;
    
    protected $table = 'attachment_types';
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 获取包含的所有附件对象
     *
     * @return HasMany
     */
    function attachments() { return $this->hasMany('App\Models\Attachment'); }
    
    /**
     * 附件类型列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'AttachmentType.id', 'dt' => 0],
            ['db' => 'AttachmentType.name', 'dt' => 1],
            ['db' => 'AttachmentType.remark', 'dt' => 2],
            ['db' => 'AttachmentType.created_at', 'dt' => 3],
            ['db' => 'AttachmentType.updated_at', 'dt' => 4],
            [
                'db'        => 'AttachmentType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        
        return Datatable::simple(
            $this, $columns
        );
        
    }
    
    /**
     * 保存附件类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新附件类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 移除附件类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->purge([class_basename($this)], 'id', 'purge', $id);
                $this->purge(['Attachment'], 'attachment_type_id', 'reset', $id);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
