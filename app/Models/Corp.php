<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Corp
 *
 * @property int $id
 * @property string $name 企业名称
 * @property string $corpid 企业号id
 * @property string $corpsecret 管理组的凭证密钥
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Corp whereCorpid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Corp whereCorpsecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Corp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Corp whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Corp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Corp whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Corp whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Corp extends Model
{
    //
}
