<?php
namespace App\Http\ViewComposers;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Models\Group;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class UserComposer
 * @package App\Http\ViewComposers
 */
class UserComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        if (Request::path() == '/' || stripos(Request::path(), 'pages') !== false) {
            $action = 'edit';
        } else {
            $action = explode('/', Request::path());
        }
        switch ($action) {
            case 'edit':
                $user = Auth::user();
                $data = [
                    'mobile'   => $user->mobiles->isNotEmpty()
                        ? $user->mobiles->where('isdefault', 1)->first()->mobile
                        : '(n/a)',
                    'disabled' => true,
                ];
                break;
            case 'event':
                $data = [
                    'titles' => [
                        '#', '名称', '备注', '地点', '开始时间', '结束时间',
                        '公共事件', '课程', '提醒', '创建者', '更新于',
                    ],
                ];
                break;
            case 'message':
                list($optionAll, $htmlCommType, $htmlApp, $htmlMessageType) = $this->messageFilters();
                $data = [
                    'titles'    => [
                        '#',
                        ['title' => '通信方式', 'html' => $htmlCommType],
                        ['title' => '应用', 'html' => $htmlApp],
                        '消息批次', '发送者',
                        ['title' => '类型', 'html' => $htmlMessageType],
                        ['title' => '接收于', 'html' => $this->inputDateTimeRange('接收于')],
                        [
                            'title' => '状态',
                            'html'  => $this->singleSelectList(
                                array_merge($optionAll, [0 => '未读', 1 => '已读']), 'filter_read'
                            ),
                        ],
                    ],
                    'batch'     => true,
                    'removable' => true,
                    'filter'    => true,
                ];
                break;
            case 'reset':
                $data = ['disabled' => true];
                break;
            default:
                $values = Constant::SUPER_ROLES;
                $ids = array_map(
                    function ($name) {
                        return Group::whereName($name)->first()->id;
                    }, $values
                );
                $role = Auth::user()->role();
                if ($role == '运营') {
                    $groups = array_combine($ids, $values);
                } elseif ($role == '企业') {
                    $groups = array_combine(
                        [$ids[1], $ids[2]],
                        [$values[1], $values[2]]
                    );
                }
                $data = ['groups' => $groups ?? []];
                break;
        }
        
        $view->with($data);
        
    }
    
}