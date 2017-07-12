<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubjectModule
 *
 * @property int $id
 * @property int $subject_id 所属科目ID
 * @property string $name 科目次分类名称
 * @property int $weight 科目次分类权重
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectModule whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectModule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectModule whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectModule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectModule whereWeight($value)
 * @mixin \Eloquent
 */
class SubjectModule extends Model
{
    //
}
