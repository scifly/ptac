<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MediaType
 *
 * @property int $id
 * @property string $name 媒体类型名称
 * @property string $remark 媒体类型备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|MediaType whereCreatedAt($value)
 * @method static Builder|MediaType whereEnabled($value)
 * @method static Builder|MediaType whereId($value)
 * @method static Builder|MediaType whereName($value)
 * @method static Builder|MediaType whereRemark($value)
 * @method static Builder|MediaType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MediaType extends Model {
    //
    protected $table = 'media_types';

    protected $fillable = ['name','remark','created_at','updated_at','enabled'];
}
