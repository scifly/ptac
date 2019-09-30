<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Facades\Request;
use Throwable;

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
 * @method static Builder|SubjectModule newModelQuery()
 * @method static Builder|SubjectModule newQuery()
 * @method static Builder|SubjectModule query()
 * @mixin Eloquent
 */
class SubjectModule extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['subject_id', 'name', 'weight', 'enabled'];
    
    /** @return BelongsTo */
    function subject() { return $this->belongsTo('App\Models\Subject'); }
    
    /**
     * 科目次分类列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'SubjectModule.id', 'dt' => 0],
            ['db' => 'Subject.name as subjectname', 'dt' => 1],
            ['db' => 'SubjectModule.name', 'dt' => 2],
            ['db' => 'SubjectModule.weight', 'dt' => 3],
            ['db' => 'SubjectModule.created_at', 'dt' => 4],
            ['db' => 'SubjectModule.updated_at', 'dt' => 5],
            [
                'db'        => 'SubjectModule.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'subjects',
                'alias'      => 'Subject',
                'type'       => 'INNER',
                'conditions' => [
                    'Subject.id = SubjectModule.subject_id',
                ],
            ],
        ];
        $condition = 'Subject.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存科目次分类
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新科目次分类
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
    
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * （批量）删除科目次分类
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id);
        
    }
    
    /** @return array */
    function compose() {
    
        if (explode('/', Request::path())[1] == 'index') {
            $data = [
                'titles' => ['#', '科目名称', '次分类名称', '次分类权重', '创建于', '更新于', '状态 . 操作'],
            ];
        } else {
            $builder = Subject::where(['school_id' => $this->schoolId(), 'enabled' => 1]);
            $data = ['subjects' => $builder->pluck('name', 'id')];
        }
        
        return $data;
        
    }
    
}
