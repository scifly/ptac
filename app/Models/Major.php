<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\BelongsToMany};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * App\Models\Major
 *
 * @property int $id
 * @property string $name 专业名称
 * @property string $remark 专业备注
 * @property int $school_id 所属学校ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $enabled
 * @property-read MajorSubject $majorSubject
 * @property-read School $school
 * @property-read Collection|Subject[] $subjects
 * @property-read int|null $subjects_count
 * @method static Builder|Major whereCreatedAt($value)
 * @method static Builder|Major whereEnabled($value)
 * @method static Builder|Major whereId($value)
 * @method static Builder|Major whereName($value)
 * @method static Builder|Major whereRemark($value)
 * @method static Builder|Major whereSchoolId($value)
 * @method static Builder|Major whereUpdatedAt($value)
 * @method static Builder|Major newModelQuery()
 * @method static Builder|Major newQuery()
 * @method static Builder|Major query()
 * @mixin Eloquent
 */
class Major extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['school_id', 'name', 'remark', 'enabled'];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return BelongsToMany */
    function subjects() { return $this->belongsToMany('App\Models\Subject', 'major_subject'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * 专业列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Major.id', 'dt' => 0],
            [
                'db'        => 'Major.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return $this->iconHtml('fa-graduation-cap') . $d;
                },
            ],
            ['db' => 'Major.remark', 'dt' => 2],
            ['db' => 'Major.created_at', 'dt' => 3],
            ['db' => 'Major.updated_at', 'dt' => 4],
            [
                'db'        => 'Major.enabled', 'dt' => 5,
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
                    'School.id = Major.school_id',
                ],
            ],
        ];
        $condition = 'Major.school_id = ' . $this->schoolId();
        
        return DataTable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存专业
     *
     * @param array $data
     * @return bool|mixed
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                $major = $this->create($data);
                $this->retain('MajorSubject', $major->id, $data['subject_ids'] ?? []);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新专业
     *
     * @param array $data
     * @param $id
     * @return bool|mixed
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise(
            $this, $data, $id,
            function (Major $major) use ($data, $id) {
                $major->retain('MajorSubject', $id, $data['subject_ids'] ?? []);
            }
        );
        
    }
    
    /**
     * 删除专业
     *
     * @param $id
     * @return bool|mixed
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->purge(
                    ['Major', 'MajorSubject'],
                    'major_id', 'purge', $id
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 返回composer所需view数据
     *
     * @return array
     */
    function compose() {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = ['titles' => ['#', '名称', '备注', '创建于', '更新于', '状态 . 操作']];
        } else {
            $major = Major::find(Request::route('id'));
            $subjects = $major ? $major->subjects : null;
            $data = [
                'subjects'         => Subject::whereSchoolId($this->schoolId())->pluck('name', 'id'),
                'selectedSubjects' => $subjects ? $subjects->pluck('id') : null,
            ];
        }
    
        return $data;
        
    }
    
}
