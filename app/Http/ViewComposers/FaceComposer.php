<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Face;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;

/**
 * Class FaceComposer
 * @package App\Http\ViewComposers
 */
class FaceComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'buttons' => [
                    'create' => [
                        'id'    => 'batch-create',
                        'label' => '设置人脸识别',
                        'icon'  => 'fa fa-user-md',
                        'title' => '设置',
                    ],
                    'edit'   => [
                        'id'    => 'batch-edit',
                        'label' => '修改(黑名单/白名单)',
                        'icon'  => 'fa fa-asterisk',
                        'title' => '更新',
                    ],
                ],
                'titles'  => [
                    '#', 'faceid', '头像', '姓名', '角色',
                    [
                        'title' => '创建于',
                        'html'  => $this->inputDateTimeRange('创建于'),
                    ],
                    [
                        'title' => '更新于',
                        'html'  => $this->inputDateTimeRange('更新于'),
                    ],
                    [
                        'title' => '状态 . 操作',
                        'html'  => $this->singleSelectList(
                            [null => '全部', 0 => '未设置', 1 => '白名单', 2 => '黑名单', 3 => 'VIP'], 'filter_state'
                        ),
                    ],
                ],
                'batch'   => true,
                'filter'  => true,
            ];
        } else {
            $action = Request::route()->getActionMethod();
            $ids = Request::route('id') ? [Request::route('id')] : Request::get('ids');
            if ($ids) {
                session(['ids' => $ids]);
            } else {
                $ids = session('ids');
            }
            $users = User::whereIn('id', $ids)->get()->when(
                $action == 'create', function (Collection $users) {
                   return $users->where('face_id', 0);
                }
            );
            $face = new Face;
            $row = <<<HTML
                <tr>
                    <td>%s</td>
                    <td class="text-center">%s</td>
                    <td class="text-center">%s</td>
                    <td class="text-center">%s</td>
                    <td class="text-center">%s</td>%s
                </tr>
            HTML;
            $list = ''; $i = 0;
            foreach ($users as $user) {
                $state = $action == 'create' ? ''
                    : '<td>' . $face->status($user->card ? $user->card->status : 1) . '</td>';
                $default = $user->mobiles->where('isdefault', 1)->first();
                $record = sprintf(
                    $row,
                    $user->id,
                    $user->realname,
                    $user->group->name,
                    $default ? $default->mobile : 'n/a',
                    $user->id, $i,
                    $user->card ? $user->card->sn : '',
                    $state
                );
        
                $list .= $record;
                $i++;
            }
            !empty($list)
                ?: $list = '<tr><td colspan="6" class="text-center text-red">- 已设置人脸识别 -</td></tr>';
            $data = [
                'list' => $list,
                'edit' => $action == 'edit' ? true : null
            ];
        }
        
        $view->with($data);
    }
    
}