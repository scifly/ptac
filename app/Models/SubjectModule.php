<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SubjectModule 科目次分类
 *
 * @property int $id
 * @property int $subject_id 所属科目ID
 * @property string $name 科目次分类名称
 * @property int $weight 科目次分类权重
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Subject $subject
 * @method static Builder|SubjectModule whereCreatedAt($value)
 * @method static Builder|SubjectModule whereEnabled($value)
 * @method static Builder|SubjectModule whereId($value)
 * @method static Builder|SubjectModule whereName($value)
 * @method static Builder|SubjectModule whereSubjectId($value)
 * @method static Builder|SubjectModule whereUpdatedAt($value)
 * @method static Builder|SubjectModule whereWeight($value)
 * @mixin Eloquent
 */
class SubjectModule extends Model {
    
    use ModelTrait;

    protected $table = 'subject_modules';
    protected $fillable = ['subject_id', 'name', 'weight', 'enabled'];
    
    /**
     * 返回科目次分类所属的科目对象
     * 
     * @return BelongsTo
     */
    public function subject() { return $this->belongsTo('App\Models\Subject'); }
    
    /**
     * 科目次分类列表
     *
     * @return array
     */
    public function datatable() {
        
        $columns = [
            ['db' => 'SubjectModule.id', 'dt' => 0],
            ['db' => 'Subject.name as subjectname', 'dt' => 1],
            ['db' => 'SubjectModule.name', 'dt' => 2],
            ['db' => 'SubjectModule.weight', 'dt' => 3],
            ['db' => 'SubjectModule.created_at', 'dt' => 4],
            ['db' => 'SubjectModule.updated_at', 'dt' => 5],
            [
                'db' => 'SubjectModule.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'subjects',
                'alias' => 'Subject',
                'type' => 'INNER',
                'conditions' => [
                    'Subject.id = SubjectModule.subject_id',
                ],
            ],
        ];
        $condition = 'Subject.school_id = ' . $this->schoolId();
        
        return Datatable::simple(self::getModel(), $columns, $joins, $condition);

    }

}
