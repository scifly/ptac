<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\MessageType
 *
 * @property int $id
 * @property string $name 消息类型名称
 * @property string $remark 消息类型备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|MessageType whereCreatedAt($value)
 * @method static Builder|MessageType whereEnabled($value)
 * @method static Builder|MessageType whereId($value)
 * @method static Builder|MessageType whereName($value)
 * @method static Builder|MessageType whereRemark($value)
 * @method static Builder|MessageType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Message[] $message
 * @property-read Collection|Message[] $messages
 */
class MessageType extends Model {

    use ModelTrait;

    protected $table = 'message_types';

    protected $fillable = ['name', 'remark', 'enabled'];

    /**
     * 获取指定消息类型包含的所有消息对象
     *
     * @return HasMany
     */
    public function messages() { return $this->hasMany('App\Models\Message'); }

    /**
     * 保存消息类型
     *
     * @param array $data
     * @return bool
     */
    public function store(array $data) {

        $messageType = $this->create($data);

        return $messageType ? true : false;

    }

    /**
     * 更新消息类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    public function modify(array $data, $id) {

        $messageType = $this->find($id);
        if (!$messageType) { return false; }

        return $messageType->update($data) ? true : false;

    }
    
    /**
     * 删除消息类型
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    public function remove($id) {

        $messageType = $this->find($id);
        if (!$messageType) { return false; }
        
        return $messageType->removable($messageType) ? $messageType->delete() : false;

    }

    public function datatable() {

        $columns = [
            ['db' => 'MessageType.id', 'dt' => 0],
            ['db' => 'MessageType.name', 'dt' => 1],
            ['db' => 'MessageType.remark', 'dt' => 2],
            ['db' => 'MessageType.created_at', 'dt' => 3],
            ['db' => 'MessageType.updated_at', 'dt' => 4],
            [
                'db' => 'MessageType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];

        return Datatable::simple($this, $columns);

    }

}
