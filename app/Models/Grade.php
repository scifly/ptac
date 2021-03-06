<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\HasMany,
    Relations\HasManyThrough};
use Illuminate\Support\Facades\{Auth, DB, Request};
use Throwable;

/**
 * App\Models\Grade 年级
 *
 * @property int $id
 * @property string $name 年级名称
 * @property int $school_id 所属学校ID
 * @property string $educator_ids 年级主任教职员工ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $department_id 对应的部门ID
 * @property-read Collection|Squad[] $classes
 * @property-read Department $dept
 * @property-read School $school
 * @property-read Collection|Student[] $students
 * @property-read int|null $classes_count
 * @property-read int|null $students_count
 * @method static Builder|Grade whereCreatedAt($value)
 * @method static Builder|Grade whereDepartmentId($value)
 * @method static Builder|Grade whereEducatorIds($value)
 * @method static Builder|Grade whereEnabled($value)
 * @method static Builder|Grade whereId($value)
 * @method static Builder|Grade whereName($value)
 * @method static Builder|Grade whereSchoolId($value)
 * @method static Builder|Grade whereUpdatedAt($value)
 * @method static Builder|Grade newModelQuery()
 * @method static Builder|Grade newQuery()
 * @method static Builder|Grade query()
 * @mixin Eloquent
 */
class Grade extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'school_id', 'department_id', 'name',
        'educator_ids', 'enabled',
    ];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return BelongsTo */
    function dept() { return $this->belongsTo('App\Models\Department', 'department_id'); }
    
    /** @return HasMany */
    function classes() { return $this->hasMany('App\Models\Squad'); }
    
    /** @return HasManyThrough */
    function students() {
        
        return $this->hasManyThrough(
            'App\Models\Student',
            'App\Models\Squad',
            'grade_id',
            'class_id');
        
    }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * 年级列表
     *
     * @return array
     * @throws Exception
     */
    function index() {
        
        $columns = [
            ['db' => 'Grade.id', 'dt' => 0],
            [
                'db'        => 'Grade.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return $this->iconHtml($d, 'grade');
                },
            ],
            [
                'db'        => 'Grade.educator_ids', 'dt' => 2,
                'formatter' => function ($d) {
                    return Educator::with('user')
                        ->whereIn('id', explode(',', $d))
                        ->get()->pluck('user.realname')->join(', ');
                },
            ],
            ['db' => 'Grade.created_at', 'dt' => 3],
            ['db' => 'Grade.updated_at', 'dt' => 4],
            [
                'db'        => 'Department.synced as synced', 'dt' => 5,
                'formatter' => function ($d) {
                    return $d ? '是' : '否';
                },
            ],
            [
                'db'        => 'Grade.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = Grade.school_id',
                ],
            ],
            [
                'table'      => 'departments',
                'alias'      => 'Department',
                'type'       => 'INNER',
                'conditions' => [
                    'Department.id = Grade.department_id',
                ],
            ],
        ];
        $condition = 'School.id = ' . $this->schoolId();
        if (!in_array(Auth::user()->role(), Constant::SUPER_ROLES)) {
            $condition .= ' AND Grade.id IN (' . $this->gradeIds()->join(',') . ')';
        }
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存年级
     *
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                # 创建年级
                $grade = $this->create($data);
                # 更新年级的部门id
                $department = (new Department)->stow($grade, 'school');
                $grade->update(['department_id' => $department->id]);
                # 更新部门标签绑定关系
                (new DepartmentTag)->storeByDeptId(
                    $department->id, $data['tag_ids'] ?? []
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新年级
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id = null) {
        
        return $this->revise(
            $this, $data, $id,
            function (Grade $grade) use ($data) {
                (new Department)->alter($grade);
                (new DepartmentTag)->storeByDeptId(
                    $grade->department_id, $data['tag_ids'] ?? []
                );
            }
        );

    }
    
    /**
     * （批量）删除年级
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->mdPurge($id, [
            'purge.grade_id'  => ['Squad'],
            'clear.grade_ids' => ['Subject'],
        ]);
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 返回view所需数据
     *
     * @return array
     * @throws Exception
     */
    function compose() {
        
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = ['titles' => ['#', '名称', '年级主任', '创建于', '更新于', '同步状态', '状态 . 操作']];
        } else {
            $educators = Educator::where(['school_id' => $this->schoolId(), 'enabled' => 1])
                ->with('user')->get()->pluck('user.realname', 'id');
            $grade = Grade::find(Request::route('id'));
            if ($grade && !empty($grade->educator_ids)) {
                $selectedEducators = collect(explode(',', $grade->educator_ids));
            }
            $data = array_merge(
                array_combine(['educators', 'selectedEducators'], [$educators, $selectedEducators ?? null]),
                (new Tag)->compose('department', $grade ? $grade->dept : null)
            );
        }
        
        return $data;
        
    }
    
    /**
     * 返回指定年级包含的班级列表html
     *
     * @param $id
     * @return array
     * @throws Exception
     */
    function classList($id) {
        
        abort_if(
            !$this->gradeIds()->flip()->has($id) || !$this->find($id),
            Constant::NOT_ACCEPTABLE,
            __('messages.not_acceptable')
        );
        $items = $this->find($id)->classes->pluck('name', 'id');
        
        return [
            $this->htmlSelect($items, 'class_id'),
            $items->keys()->first()
        ];
        
    }
    
}