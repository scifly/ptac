<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
 */
class SchoolType extends Model {
<<<<<<< HEAD
    //
    protected $table = 'school_types';
    protected $fillable =[
        'name',
        'remark',
        'created_at',
        'updated_at',
    ];
=======

    protected $fillable = [''];
    
>>>>>>> d25f36b1506eb11ca099f0ee1de8114ddbb2885f
}
