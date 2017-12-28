<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ActionType 功能HTTP请求类型
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name action类型名称
 * @property string|null $remark 备注
 * @property int $enabled
 * @method static Builder|School whereCreatedAt($value)
 * @method static Builder|School whereEnabled($value)
 * @method static Builder|School whereId($value)
 * @method static Builder|School whereName($value)
 * @method static Builder|School whereRemark($value)
 * @method static Builder|School whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ActionType extends Model {

    protected $fillable = ['name', 'remark', 'enabled'];

}

