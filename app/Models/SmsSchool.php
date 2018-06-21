<?php
namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SmsSchool 学校短信配额
 *
 * @property int $id
 * @property int $school_id 学校ID
 * @method static Builder|SmsSchool whereId($value)
 * @method static Builder|SmsSchool whereSchoolId($value)
 * @mixin Eloquent
 */
class SmsSchool extends Model {

}
