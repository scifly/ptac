<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\MajorRequest;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
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
 * @method static Builder|Major whereCreatedAt($value)
 * @method static Builder|Major whereEnabled($value)
 * @method static Builder|Major whereId($value)
 * @method static Builder|Major whereName($value)
 * @method static Builder|Major whereRemark($value)
 * @method static Builder|Major whereSchoolId($value)
 * @method static Builder|Major whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read MajorSubject $majorSubject
 * @property-read School $school
 * @property-read Collection|Subject[] $subjects
 */
class Major extends Model {
    
    use ModelTrait;
    
    protected $table = 'majors';
    
    protected $fillable = [
        'name', 'remark', 'school_id', 'enabled',
    ];
    
    /**
     * 返回专业所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定专业所包含的科目对象
     *
     * @return BelongsToMany
     */
    function subjects() {
        
        return $this->belongsToMany(
            'App\Models\Subject',
            'majors_subjects',
            'major_id',
            'subject_id'
        );
        
    }
    
    /**
     * 返回专业列表
     *
     * @return Collection
     */
    function majorList() {
        
        return self::whereSchoolId($this->schoolId())->get()->pluck('name', 'id');
        
    }
    
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
                    return sprintf(Snippet::ICON, 'fa-graduation-cap', '') . $d;
                },
            ],
            ['db' => 'Major.remark', 'dt' => 2],
            ['db' => 'Major.created_at', 'dt' => 3],
            ['db' => 'Major.updated_at', 'dt' => 4],
            [
                'db'        => 'Major.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
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
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存专业
     *
     * @param MajorRequest $request
     * @return bool|mixed
     * @throws Throwable
     */
    function store(MajorRequest $request) {
        
        try {
            DB::transaction(function () use ($request) {
                $major = $this->create($request->all());
                $subjectIds = $request->input('subject_ids', []);
                (new MajorSubject)->storeByMajorId($major->id, $subjectIds);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新专业
     *
     * @param MajorRequest $request
     * @param $id
     * @return bool|mixed
     * @throws Throwable
     */
    function modify(MajorRequest $request, $id) {
        
        try {
            DB::transaction(function () use ($request, $id) {
                $this->find($id)->update($request->all());
                $subjectIds = $request->input('subject_ids', []);
                MajorSubject::whereMajorId($id)->delete();
                (new MajorSubject)->storeByMajorId($id, $subjectIds);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除专业
     *
     * @param $id
     * @return bool|mixed
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定专业的所有数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                MajorSubject::whereMajorId($id)->delete();
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
