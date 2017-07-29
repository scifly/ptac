<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;

/**
 * App\Models\Educator
 *
 * @property int $id
 * @property int $user_id 教职员工用户ID
 * @property string $team_ids 所属组
 * @property int $school_id 所属学校ID
 * @property int $sms_quote 可用短信条数
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static Builder|Educator whereCreatedAt($value)
 * @method static Builder|Educator whereId($value)
 * @method static Builder|Educator whereSchoolId($value)
 * @method static Builder|Educator whereSmsQuote($value)
 * @method static Builder|Educator whereTeamIds($value)
 * @method static Builder|Educator whereUpdatedAt($value)
 * @method static Builder|Educator whereUserId($value)
 * @mixin \Eloquent
 */
class Educator extends Model
{

    protected $fillable = [
        'id',
        'user_id',
        'team_ids',
        'school_id',
        'sms_quote',

    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function school()
    {
        return $this->belongsTo('App\Models\School');
    }

    public function educatorClass()
    {
        return $this->hasOne('App\Models\EducatorClass');
    }

    public function datatable()
    {

        $columns = [
            ['db' => 'Educator.id', 'dt' => 0],
            ['db' => 'User.username', 'dt' => 1],
            ['db' => 'Educator.team_ids', 'dt' => 2],
            ['db' => 'Shool.name', 'dt' => 3],
            ['db' => 'Educator.sms_quote', 'dt' => 4],
            ['db' => 'Educator.created_at', 'dt' => 5],
            ['db' => 'Educator.updated_at', 'dt' => 6],

            [
                'db' => 'Educator.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Educator.user_id'
                ]
            ],
            [
                'table' => 'schools',
                'alias' => 'Shool',
                'type' => 'INNER',
                'conditions' => [
                    'Shool.id = Educator.school_id'
                ]
            ]
        ];

        return Datatable::simple($this, $columns, $joins);
    }

}

