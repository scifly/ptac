<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AlertType 警告类型
 *
 * @property int $id
 * @property string $name 提前提醒的时间
 * @property string $english_name 提前提醒时间的英文名称
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|AlertType whereCreatedAt($value)
 * @method static Builder|AlertType whereEnabled($value)
 * @method static Builder|AlertType whereEnglishName($value)
 * @method static Builder|AlertType whereId($value)
 * @method static Builder|AlertType whereName($value)
 * @method static Builder|AlertType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AlertType extends Model {

    protected $fillable = ['name', 'english_name', 'enabled'];
   
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 警告类型列表
     *
     * @return array
     */
    function datatable() {

        $columns = [
            ['db' => 'AlertType.id', 'dt' => 0],
            ['db' => 'AlertType.name', 'dt' => 1],
            ['db' => 'AlertType.english_name', 'dt' => 2],
            ['db' => 'AlertType.created_at', 'dt' => 3],
            ['db' => 'AlertType.updated_at', 'dt' => 4],
            [
                'db' => 'AlertType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                }
            ],
        ];

        return Datatable::simple($this->getModel(), $columns);

    }

}
