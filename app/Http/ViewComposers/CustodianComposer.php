<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\{Card, Custodian, Educator, Grade, Squad};
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class CustodianComposer
 * @package App\Http\ViewComposers
 */
class CustodianComposer {
    
    use ModelTrait;
    
    protected $custodian, $educator;
    
    /**
     * CustodianComposer constructor.
     * @param Custodian $custodian
     * @param Educator $educator
     */
    function __construct(Custodian $custodian, Educator $educator) {
        
        $this->custodian = $custodian;
        $this->educator = $educator;
        
    }
    
    /**
     * @param View $view
     * @return void
     */
    public function compose(View $view) {
        
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
                $grades = Grade::whereIn('id', $this->gradeIds())
                    ->where('enabled', 1)
                    ->pluck('name', 'id')
                    ->toArray();
                $classes = Squad::whereGradeId(array_key_first($grades))
                    ->where('enabled', 1)
                    ->pluck('name', 'id')
                    ->toArray();
                $data = [
                    'buttons'        => [
                        'issue' => [
                            'id' => 'issue',
                            'label' => '发卡',
                            'icon' => 'fa fa-credit-card'
                        ],
                        'grant' => [
                            'id'    => 'grant',
                            'label' => '一卡通授权',
                            'icon'  => 'fa fa-credit-card',
                        ],
                        'face' => [
                            'id'    => 'face',
                            'label' => '人脸设置',
                            'icon'  => 'fa fa-camera',
                        ],
                    ],
                    'batch'   => true,
                    'filter'  => true,
                    'titles'  => [
                        '#', '姓名', '头像',
                        [
                            'title' => '性别',
                            'html' => $this->singleSelectList(
                                [null => '全部', 0 => '女', 1 => '男'], 'filter_gender'
                            )
                        ],
                        '学生', '手机号码',
                        [
                            'title' => '创建于',
                            'html' => $this->inputDateTimeRange('创建于')
                        ],
                        [
                            'title' => '更新于',
                            'html' => $this->inputDateTimeRange('更新于')
                        ],
                        // [
                        //     'title' => '同步状态',
                        //     'html' => $this->singleSelectList(
                        //         [null => '全部', 0 => '未同步', 1 => '已同步'], 'filter_synced'
                        //     ),
                        // ],
                        // [
                        //     'title' => '关注状态',
                        //     'html' => $this->singleSelectList(
                        //         [null => '全部', 0 => '未关注', 1 => '已关注'], 'filter_subscribed'
                        //     )
                        // ],
                        [
                            'title' => '状态 . 操作',
                            'html' => $this->singleSelectList(
                                [null => '全部', 0 => '未启用', 1 => '已启用'], 'filter_enabled'
                            )
                        ],
                    ],
                    'grades'  => $grades,
                    'classes' => $classes,
                    'title'   => '导出监护人',
                ];
                break;
            case 'issue':
                $titles = <<<HTML
                    <th>#</th>
                    <th class="text-center">家长</th>
                    <th class="text-center">学生</th>
                    <th class="text-center">学号</th>
                    <th>卡号</th>
                HTML;
                $data = $this->data($titles);
                break;
            case 'grant':
                $data = (new Card)->compose('Custodian');
                break;
            case 'face':
                $titles = <<<HTML
                    <th>#</th>
                    <th class="text-center">家长</th>
                    <th class="text-center">学生</th>
                    <th class="text-center">学号</th>
                    <th>人脸</th>
                    <th>设备</th>
                    <th class="text-center">状态</th>
                HTML;
                $data = $this->data($titles);
                break;
            default:
                $data = array_combine(
                    [
                        'custodian', 'title', 'grades', 'classes', 'students',
                        'relations', 'mobiles', 'relationship'
                    ],
                    array_merge($this->custodian->compose(), [true])
                );
                break;
        }
        
        $view->with($data);
        
    }
    
    /**
     * 返回view所需数据
     *
     * @param string $titles
     * @return array
     */
    private function data(string $titles): array {
    
        $classes = Squad::whereIn('id', $this->classIds())
            ->get()->pluck('name', 'id')->toArray();
        
        return [
            'prompt' => '家长列表',
            'formId' => 'formCustodian',
            'classes' => [0 => '(请选择一个班级)'] + $classes,
            'titles' => $titles,
            'columns' => 7
        ];
        
    }
    
}