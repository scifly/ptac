<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{Auth, DB, Request};
use ReflectionException;
use Throwable;

/**
 * App\Models\Evaluate
 *
 * @property int $id
 * @property int $student_id 学生id
 * @property int $indicator_id 考核项id
 * @property int $semester_id 学期id
 * @property int $educator_id 考核人教职员工id
 * @property int $amount 加/减分值
 * @property int $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $enabled
 * @property-read Educator $educator
 * @property-read Indicator $indicator
 * @property-read Semester $semester
 * @property-read Student $student
 * @method static Builder|Evaluate newModelQuery()
 * @method static Builder|Evaluate newQuery()
 * @method static Builder|Evaluate query()
 * @method static Builder|Evaluate whereAmount($value)
 * @method static Builder|Evaluate whereRemark($value)
 * @method static Builder|Evaluate whereEnabled($value)
 * @method static Builder|Evaluate whereCreatedAt($value)
 * @method static Builder|Evaluate whereEducatorId($value)
 * @method static Builder|Evaluate whereId($value)
 * @method static Builder|Evaluate whereIndicatorId($value)
 * @method static Builder|Evaluate whereSemesterId($value)
 * @method static Builder|Evaluate whereStudentId($value)
 * @method static Builder|Evaluate whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Evaluate extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'student_id', 'indicator_id', 'semester_id',
        'educator_id', 'amount', 'remark', 'enabled',
    ];
    
    /** @return BelongsTo */
    function student() { return $this->belongsTo('App\Models\Student'); }
    
    /** @return BelongsTo */
    function indicator() { return $this->belongsTo('App\Models\Indicator'); }
    
    /** @return BelongsTo */
    function semester() { return $this->belongsTo('App\Models\Semester'); }
    
    /** @return BelongsTo */
    function educator() { return $this->belongsTo('App\Models\Educator'); }
    
    /** @return array */
    function index() {
        
        $columns = [
            ['db' => 'Evaluate.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'Indicator.name as iname', 'dt' => 2],
            ['db' => 'Semester.name as sname', 'dt' => 3],
            ['db' => 'Evaluate.amount', 'dt' => 4],
            [
                'db'        => 'Evaluate.educator_id', 'dt' => 5,
                'formatter' => function ($d) {
                    if (!$d) return '[同事检举]';
                    $user = Educator::find($d)->user;
                    
                    return $user->id == Auth::id() ? '-' : $user->realname;
                },
            ],
            ['db' => 'Evaluate.created_at', 'dt' => 6, 'dr' => true],
            ['db' => 'Evaluate.updated_at', 'dt' => 7, 'dr' => true],
            [
                'db'        => 'Evaluate.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'students',
                'alias' => 'Student',
                'type' => 'INNER',
                'conditions' => [
                    'Student.id = Evaluate.student_id'
                ]
            ],
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Student.user_id'
                ]
            ],
            [
                'table' => 'indicators',
                'alias' => 'Indicator',
                'type' => 'INNER',
                'conditions' => [
                    'Indicator.id = Evaluate.indicator_id'
                ]
            ],
            [
                'table' => 'semesters',
                'alias' => 'Semester',
                'type' => 'INNER',
                'conditions' => [
                    'Semester.id = Evaluate.semester_id'
                ]
            ],
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = Indicator.school_id',
                ],
            ],
        ];
        $condition = 'Indicator.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise($this, $data, $id);
        
    }
    
    /**
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                Request::replace(['ids' => $ids]);
                $this->purge(['Indicator', 'Evaluate'], 'indicator_id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * @return array
     * @throws ReflectionException
     */
    function compose() {
        
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            return [
                'titles' => [
                    '#', '学生', '考核项', '学期', '考核人', '+/-分数',
                    ['title' => '创建于', 'html' => $this->htmlDTRange('创建于')],
                    ['title' => '更新于', 'html' => $this->htmlDTRange('更新于')],
                    [
                        'title' => '状态 . 操作',
                        'html' => $this->htmlSelect(
                            collect([null => '全部'])->union(['无效', '有效']),
                            'filter_enabled'
                        )
                    ]
                ],
                'filter' => true,
                'batch' => true
            ];
        } else {
            $students = Student::with('user')->whereIn('id', $this->contactIds('student'))
                ->get()->pluck('user.realname', 'id');
            $schoolId = $this->schoolId();
            $indicators = Indicator::whereSchoolId($schoolId)->pluck('name', 'id');
            $semesters = Semester::whereSchoolId($schoolId)->pluck('name', 'id');
            return [
                'students' => $students,
                'indicators' => $indicators,
                'semesters' => $semesters
            ];
        }
        
    }
    
}
