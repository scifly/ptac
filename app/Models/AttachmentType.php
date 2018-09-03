<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @method static Builder|AttachmentType whereCreatedAt($value)
 * @method static Builder|AttachmentType whereEnabled($value)
 * @method static Builder|AttachmentType whereId($value)
 * @method static Builder|AttachmentType whereName($value)
 * @method static Builder|AttachmentType whereRemark($value)
 * @method static Builder|AttachmentType whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Attachment[] $attachments
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
            $this->getModel(), $columns
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
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定附件类型的所有相关数据
     *
     * @param $id
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                Attachment::whereAttachmentTypeId($id)->update([
                    'attachment_type_id' => 0,
                ]);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}
