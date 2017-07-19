<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Grade
 *
 * @property int $id
 * @property string $name 年级名称
 * @property int $school_id 所属学校ID
 * @property string $educator_ids 年级主任教职员工ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Grade whereCreatedAt($value)
 * @method static Builder|Grade whereEducatorIds($value)
 * @method static Builder|Grade whereEnabled($value)
 * @method static Builder|Grade whereId($value)
 * @method static Builder|Grade whereName($value)
 * @method static Builder|Grade whereSchoolId($value)
 * @method static Builder|Grade whereUpdatedAt($value)
 * @mixin \Eloquent
 * 年级
 */
class Grade extends Model {
    protected $table = 'grade';

    //
    protected $fillable = [
        'name',
        'school_id',
        'educator_ids',
        'enabled',
    ];
    public function hasManySquad()
    {

        return $this->hasMany('App\Models\Squad');
    }
    public function datatable()
    {
        $columns = [
            ['db' => 'Grade.id', 'dt' => 0],
            ['db' => 'Grade.name', 'dt' => 1],
            ['db' => 'School.name', 'dt' => 2],
            ['db' => 'User.username', 'dt' => 3],
            [
                'db' => 'School.enabled', 'dt' => 6,
                'formatter' => function($d, $row) {
                    return $this->_dtOps($this, $d, $row);
                }
            ]
        ];

}
