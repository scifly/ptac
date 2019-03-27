<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\{Educator, Grade, Squad};
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;
use ReflectionException;

/**
 * Class SquadComposer
 * @package App\Http\ViewComposers
 */
class SquadComposer {
    
    use ModelTrait;
    
    protected $educator;
    
    /**
     * SquadComposer constructor.
     * @param Educator $educator
     */
    function __construct(Educator $educator) {
        
        $this->educator = $educator;
        
    }
    
    /**
     * @param View $view
     * @throws ReflectionException
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'titles' => [
                    '#', '名称',
                    [
                        'title' => '所属年级',
                        'html'  => $this->singleSelectList(
                            [null => '全部'] + Grade::whereIn('id', $this->gradeIds())
                                ->pluck('name', 'id')->toArray(),
                            'filter_grade_id'
                        )
                    ],
                    '班主任',
                    [
                        'title' => '创建于',
                        'html'  => $this->inputDateTimeRange('创建于')
                    ],
                    [
                        'title' => '更新于',
                        'html'  => $this->inputDateTimeRange('更新于')
                    ],
                    [
                        'title' => '同步状态',
                        'html'  => $this->singleSelectList(
                            [null => '全部', 0 => '未同步', 1 => '已同步'], 'filter_subscribed'
                        )
                    ],
                    [
                        'title' => '状态 . 操作',
                        'html'  => $this->singleSelectList(
                            [null => '全部', 0 => '未启用', 1 => '已启用'], 'filter_enabled'
                        )
                    ],
                ],
                'filter' => true
            ];
        } else {
            $grades = Grade::whereIn('id', $this->gradeIds())
                ->where('enabled', 1)
                ->pluck('name', 'id');
            $educators = Educator::whereIn('id', $this->contactIds('educator'))
                ->where('enabled', 1)->with('user')
                ->get()->pluck('user.realname', 'id');
            if (Request::route('id')) {
                $educatorIds = Squad::find(Request::route('id'))->educator_ids;
                $educatorIds == '0' ?: $selectedEducators = $this->educator->educatorList(
                    explode(',', $educatorIds)
                );
            }
            $data = [
                'grades'            => $grades,
                'educators'         => $educators,
                'selectedEducators' => $selectedEducators ?? null,
            ];
        }
        
        $view->with($data);
        
    }
    
}