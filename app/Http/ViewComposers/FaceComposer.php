<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\{Camera, Face, User};
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
                        'label' => '批量设置',
                        'icon'  => 'fa fa-camera-retro',
                        'title' => '设置',
                    ]
                ],
                'titles'  => [
                    '#', '人脸', '姓名', '角色',
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
                in_array($action, ['create', 'edit']),
                function (Collection $users) use ($action) {
                    return $users->where('face_id', $action == 'create' ? '=' : '<>', 0);
                }
            );
            $face = new Face;
            $tpl = <<<HTML
                <tr>
                    <td>%s</td>
                    <td class="text-center">%s</td>
                    <td class="text-center">%s</td>
                    <td class="text-center">%s</td>
                    <td>%s</td><td>%s</td>
                    <td class="text-center">%s</td>
                </tr>
            HTML;
            $list = '';
            $cameras = (new Camera)->cameras();
            /** @var User $user */
            foreach ($users as $user) {
                $default = $user->mobiles->where('isdefault', 1)->first();
                $list .= sprintf(
                    $tpl,
                    $user->id, $user->realname, $user->group->name,
                    $default ? $default->mobile : 'n/a',
                    $face->uploader($user), $face->selector($cameras, $user),
                    $face->state($user->face ? $user->face->state : 1, $user->id)
                );
            }
            !empty($list) ?:
                $list = '<tr><td colspan="6" class="text-center text-red">- 已设置人脸识别 -</td></tr>';
            $data = ['list' => $list];
        }
        
        $view->with($data);
    }
    
}