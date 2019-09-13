<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\{Carbon, Facades\DB, Facades\Request};
use ReflectionException;
use Throwable;

/**
 * Class Bed
 *
 * @package App\Models
 * @property int $id
 * @property int $room_id 所属寝室id
 * @property int $student_id 所属学生id
 * @property string $name 床位号
 * @property int|null $position 0 - 下铺，1 - 上铺
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
        
        try {
            DB::transaction(function () use ($data, $id) {
                throw_if(
                    !$bed = $this->find($id),
                    new Exception(__('messages.not_found'))
                );
                $bed->update($data);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
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
                $this->purge(['Bed'], 'bed_id');
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