<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\{Carbon, Facades\Request};
use ReflectionException;
use Throwable;

/**
 * Class Bed
 *
 * @property int $id
 * @property int $room_id 所属寝室id
 * @property int $student_id 所属学生id
 * @property string $name 床位号
 * @property int|null $position 0: -，1: 上铺, 2: 下铺
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled 状态
 * @property-read Room $room
 * @property-read Student $student
 * @method static Builder|Bed newModelQuery()
 * @method static Builder|Bed newQuery()
 * @method static Builder|Bed query()
 * @method static Builder|Bed whereCreatedAt($value)
 * @method static Builder|Bed whereEnabled($value)
 * @method static Builder|Bed whereId($value)
 * @method static Builder|Bed whereName($value)
 * @method static Builder|Bed wherePosition($value)
 * @method static Builder|Bed whereRemark($value)
 * @method static Builder|Bed whereRoomId($value)
 * @method static Builder|Bed whereStudentId($value)
 * @method static Builder|Bed whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Bed extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'room_id', 'student_id', 'name',
        'position', 'remark', 'enabled'
    ];
    
    /** @return BelongsTo */
    function student() { return $this->belongsTo('App\Models\Student'); }
    
    /** @return BelongsTo */
    function room() { return $this->belongsTo('App\Models\Room'); }
    
    /** @return array */
    function index() {
        
        $columns = [
            ['db' => 'Bed.id', 'dt' => 0],
            ['db' => 'Bed.name', 'dt' => 1],
            ['db' => 'Room.name as rname', 'dt' => 2],
            ['db' => 'User.realname', 'dt' => 3],
            [
                'db' => 'Bed.position', 'dt' => 4,
                'formatter' => function ($d) {
                    return !$d ? '-' : ($d == 1 ? '上铺' : '下铺');
                }
            ],
            ['db' => 'Bed.created_at', 'dt' => 5, 'dr' => true],
            ['db' => 'Bed.updated_at', 'dt' => 6, 'dr' => true],
            [
                'db' => 'Bed.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                }
            ],
        ];
        $joins = [
            [
                'table' => 'rooms',
                'alias' => 'Room',
                'type' => 'INNER',
                'conditions' => [
                    'Room.id = Bed.room_id'
                ]
            ],
            [
                'table' => 'buildings',
                'alias' => 'Building',
                'type' => 'INNER',
                'conditions' => [
                    'Building.id = Room.building_id'
                ]
            ],
            [
                'table' => 'students',
                'alias' => 'Student',
                'type' => 'INNER',
                'conditions' => [
                    'Student.id = Bed.student_id'
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
        ];
        $condition = 'Building.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存床位
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新床位
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
     * 删除床位
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
    
        return $this->purge($id);
    
    }
    
    /**
     * @return array
     * @throws ReflectionException
     */
    function compose() {
    
        $nil = collect([0 => '全部']);
        $students = Student::with('user')->whereIn(
            'id', $this->contactIds('student')
        )->get()->pluck('user.realname', 'id');
    
        return explode('/', Request::path())[1] == 'index'
            ? [
                'titles' => [
                    '#', '名称','所属寝室', '学生',
                    [
                        'title' => '类型',
                        'html' => $this->htmlSelect($nil->union(['-', '下铺', '上铺']), 'filter_position')
                    ],
                    '创建于', '更新于', '状态 . 操作'
                ],
                'filter' => true,
                'batch' => true
            ]
            : [
                'rooms' => (new Room)->rooms('住宿'),
                'students' => $students
            ];
        
    }
    
}