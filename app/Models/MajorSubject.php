<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MajorSubject
 *
 * @property int $id
 * @property int $major_id 专业ID
 * @property int $subject_id 科目ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MajorSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MajorSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MajorSubject whereMajorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MajorSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MajorSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MajorSubject extends Model {
    
    protected $table = 'majors_subjects';
    
}
