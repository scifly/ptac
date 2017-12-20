<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\MajorRequest;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Major
 *
 * @property int $id
 * @property string $name 专业名称
 * @property string $remark 专业备注
 * @property int $school_id 所属学校ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $enabled
 * @method static Builder|Major whereCreatedAt($value)
 * @method static Builder|Major whereEnabled($value)
 * @method static Builder|Major whereId($value)
 * @method static Builder|Major whereName($value)
 * @method static Builder|Major whereRemark($value)
 * @method static Builder|Major whereSchoolId($value)
 * @method static Builder|Major whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read MajorSubject $majorSubject
 * @property-read School $school
 * @property-read Collection|Subject[] $subjects
 */
class Major extends Model {

    protected $table = 'majors';

    protected $fillable = [
        'name', 'remark', 'school_id', 'enabled',
    ];

    /**
     * 返回专业所属的学校对象
     *
     * @return BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }

    /**
     * 获取指定专业所包含的科目对象
     *
     * @return BelongsToMany
     */
    public function subjects() {

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
     * @param null $schoolId
     * @return Collection|\Illuminate\Support\Collection
     */
    public function majors($schoolId = null) {

        if (isset($schoolId)) {
            return $this->where('school_id', $schoolId)->get()->pluck('id', 'name');
        }

        return $this->pluck('id', 'name');

    }
    
    /**
     * 保存专业
     *
     * @param MajorRequest $request
     * @return bool|mixed
     * @throws Exception
     * @throws \Throwable
     */
    public function store(MajorRequest $request) {

        try {
            DB::transaction(function () use ($request) {
                $m = $this->create($request->all());
                $majorSubject = new MajorSubject();
                $subjectIds = $request->input('subject_ids', []);
                $majorSubject->storeByMajorId($m->id, $subjectIds);
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
     * @throws Exception
     * @throws \Throwable
     */
    public function modify(MajorRequest $request, $id) {

        $major = $this->find($id);
        if (!isset($major)) {
            return false;
        }
        try {
            DB::transaction(function () use ($request, $id, $major) {
                $major->update($request->all());
                $subjectIds = $request->input('subject_ids', []);
                $majorSubject = new MajorSubject();
                $majorSubject::whereMajorId($id)->delete();
                $majorSubject->storeByMajorId($id, $subjectIds);
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
     * @throws Exception
     * @throws \Throwable
     */
    public function remove($id) {

        $major = $this->find($id);
        if (!isset($major)) {
            return false;
        }
        try {
            DB::transaction(function () use ($id, $major) {
                # 删除指定的专业记录
                $major->delete();
                # 删除与指定专业绑定的科目记录
                MajorSubject::whereMajorId($id)->delete();
            });

        } catch (Exception $e) {
            throw $e;
        }
        return true;
    }

    public function datatable() {

        $columns = [
            ['db' => 'Major.id', 'dt' => 0],
            [
                'db' => 'Major.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return '<i class="fa fa-graduation-cap"></i>&nbsp;' . $d;
                }
            ],
            [
                'db' => 'School.name as schoolname', 'dt' => 2,
                'formatter' => function ($d) {
                    return '<i class="fa fa-university"></i>&nbsp;' . $d;
                }
            ],
            ['db' => 'Major.remark', 'dt' => 3],
            ['db' => 'Major.created_at', 'dt' => 4],
            [
                'db' => 'Major.updated_at', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Major.school_id',
                ],
            ],
        ];
        $school = new School();
        $schoolId = $school->getSchoolId();
        $condition = 'Major.school_id = ' . $schoolId;
        unset($school);
        return DataTable::simple($this, $columns, $joins, $condition);

    }

}
