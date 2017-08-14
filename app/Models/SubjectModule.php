<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubjectModule
 *
 * @property int $id
 * @property int $subject_id 所属科目ID
 * @property string $name 科目次分类名称
 * @property int $weight 科目次分类权重
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|SubjectModule whereCreatedAt($value)
 * @method static Builder|SubjectModule whereEnabled($value)
 * @method static Builder|SubjectModule whereId($value)
 * @method static Builder|SubjectModule whereName($value)
 * @method static Builder|SubjectModule whereSubjectId($value)
 * @method static Builder|SubjectModule whereUpdatedAt($value)
 * @method static Builder|SubjectModule whereWeight($value)
 * @mixin \Eloquent
 * @property int $wap_site_id 所属微网站ID
 * @property int $media_id 模块图片多媒体ID
 * @property-read \App\Models\Subject $belongsToWs
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectModule whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectModule whereWapSiteId($value)
 * @property-read \App\Models\Subject $subject
 */
class SubjectModule extends Model {
    //
    protected $table = 'subject_modules';
    protected $fillable = [
        'subject_id',
        'name',
        'weight',
        'enabled',
    ];
    
    public function subject() {
        return $this->belongsTo('App\Models\Subject', 'subject_id', 'id');
        
    }
    
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
                    return Datatable::dtOps($this, $d, $row);
                }
            ],
        
        
        ];
        
        $joins = [
            [
                'table' => 'subjects',
                'alias' => 'Subject',
                'type' => 'INNER',
                'conditions' => [
                    'Subject.id = SubjectModule.subject_id'
                ]
            ],
        
        ];
        
        return Datatable::simple($this, $columns, $joins);
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        
        if (isset($input['enabled']) && $input['enabled'] === 'on') {
            $input['enabled'] = 1;
        }
        if (!isset($input['enabled'])) {
            $input['enabled'] = 0;
        }
        
        $this->replace($input);
    }
}
