<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Form;
use Html;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{DB, Request};
use Illuminate\Support\HtmlString;
use Throwable;

/**
 * App\Models\FlowType 审批流程
 *
 * @property int $id
 * @property int $school_id 所属学校id
 * @property string $name 流程名称
 * @property mixed $steps 流程步骤
 * @property string $remark 流程备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Flow[] $flows
 * @property-read int|null $flows_count
 * @property-read School $school
 * @method static Builder|FlowType newModelQuery()
 * @method static Builder|FlowType newQuery()
 * @method static Builder|FlowType query()
 * @method static Builder|FlowType whereCreatedAt($value)
 * @method static Builder|FlowType whereEnabled($value)
 * @method static Builder|FlowType whereId($value)
 * @method static Builder|FlowType whereName($value)
 * @method static Builder|FlowType whereRemark($value)
 * @method static Builder|FlowType whereSchoolId($value)
 * @method static Builder|FlowType whereSteps($value)
 * @method static Builder|FlowType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class FlowType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['school_id', 'name', 'steps', 'remark', 'enabled'];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return HasMany */
    function flows() { return $this->hasMany('App\Models\Flow'); }
    
    /**
     * 审批流程列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'FlowType.id', 'dt' => 0],
            ['db' => 'FlowType.name', 'dt' => 1],
            ['db' => 'FlowType.remark', 'dt' => 2],
            ['db' => 'FlowType.created_at', 'dt' => 3],
            ['db' => 'FlowType.updated_at', 'dt' => 4],
            [
                'db'        => 'FlowType.enabled', 'dt' => 5,
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
                    'School.id = FlowType.school_id',
                ],
            ],
        ];
        $condition = 'School.id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存审批流程
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新审批流程
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id = null) {
    
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * 删除审批流程
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id, [
            'purge.flow_type_id' => ['Flow']
        ]);
        
    }
    
    /** @return array */
    function compose() {
        
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = ['titles' => ['#', '名称', '备注', '创建于', '更新于', '状态 . 操作']];
        } else {
            if ($flowType = $this->find(Request::route('id'))) {
                $steps = '';
                foreach (json_decode($flowType->steps, true) ?? [] as $step) {
                    $step['items'] = Educator::with('user')
                        ->whereIn('id', explode(',', $step['ids']))
                        ->get()->pluck('user.realname', 'id')->toArray();
                    $steps .= $this->step($step);
                }
            }
            $data = ['steps' => $steps ?? $this->step()];
        }
        
        return $data;
        
    }
    
    /**
     * @param array $step
     * @return JsonResponse|string
     */
    function step($step = []) {
        
        if ($term = Request::query('term')) {
            $keyword = '%' . $term . '%';
            $educators = DB::table('educators')
                ->leftJoin('users', 'users.id', '=', 'educators.user_id')
                ->where('users.realname', 'like', $keyword)
                ->orWhere('users.mobile', 'like', $keyword)
                ->pluck('users.realname', 'educators.id');
            foreach ($educators as $id => $text) {
                $data['results'][] = ['id' => $id, 'text' => $text];
            }
            $data = response()->json($data ?? []);
        } else {
            $name = Form::text('names[]', $step['name'] ?? null, [
                'class' => 'form-control', 'required' => 'true'
            ]);
            $ids = Form::select(
                'educator_ids[]',
                $step['items'] ?? [],
                $step['ids'] ?? [],
                [
                    'multiple' => 'multiple',
                    'class' => 'form-control',
                    'style' => 'width: 100%;',
                    'required' => 'true',
                ]
            );
            $btn = Form::button(
                Html::tag('i', '', ['class' => 'fa fa-minus text-blue']),
                ['class' => 'btn btn-box-tool remove-step', 'title' => '移除']
            );
            $tds = array_map(
                function (HtmlString $input, $attr) {
                    return Html::tag(
                        'td', $input->toHtml(),
                        empty($attr) ? [] : ['class' => $attr]
                    )->toHtml();
                }, [$name, $ids, $btn], ['', '', 'text-center']
            );
            $data = Html::tag('tr', join($tds))->toHtml();
        }
        
        return $data;
        
    }
    
}

