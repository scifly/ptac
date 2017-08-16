<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;

/**
 * App\Models\MessageType
 *
 * @property int $id
 * @property string $name 消息类型名称
 * @property string $remark 消息类型备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|MessageType whereCreatedAt($value)
 * @method static Builder|MessageType whereEnabled($value)
 * @method static Builder|MessageType whereId($value)
 * @method static Builder|MessageType whereName($value)
 * @method static Builder|MessageType whereRemark($value)
 * @method static Builder|MessageType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $message
 */
class MessageType extends Model {
    //
    protected $table = 'message_types';
    
    protected $fillable = ['name', 'remark', 'created_at', 'updated_at', 'enabled'];
    
    public function message() {
        return $this->hasMany('App\Models\Message');
    }
    /**
     * @return array
     */
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
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];

        return Datatable::simple($this, $columns);
    }
}
