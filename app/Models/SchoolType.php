<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SchoolType
 *
 * @property int $id
 * @property string $name 学校类型名称
 * @property string $remark 学校类型备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|SchoolType whereCreatedAt($value)
 * @method static Builder|SchoolType whereEnabled($value)
 * @method static Builder|SchoolType whereId($value)
 * @method static Builder|SchoolType whereName($value)
 * @method static Builder|SchoolType whereRemark($value)
 * @method static Builder|SchoolType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Collection|School[] $schools
 */
class SchoolType extends Model {
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 获取指定学校类型下属的所有学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schools() { return $this->hasMany('App\Models\School'); }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'SchoolType.id', 'dt' => 0],
            ['db' => 'SchoolType.name', 'dt' => 1],
            ['db' => 'SchoolType.remark', 'dt' => 2],
            ['db' => 'SchoolType.created_at', 'dt' => 3],
            ['db' => 'SchoolType.updated_at', 'dt' => 4],
            [
                'db'        => 'SchoolType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        
        return Datatable::simple($this, $columns);
        
    }
    
}
