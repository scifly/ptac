<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Card;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;

/**
 * Class CardComposer
 * @package App\Http\ViewComposers
 */
class CardComposer {
    
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
                        'label' => '发卡',
                        'icon'  => 'fa fa-credit-card',
                        'title' => '发卡',
                    ],
                    'edit'   => [
                        'id'    => 'batch-edit',
                        'label' => '更新(挂失/解挂)',
                        'icon'  => 'fa fa-asterisk',
                        'title' => '更新',
                    ],
                ],
                'titles'  => [
                    '#', '卡号', '通行权限', '持卡人', '角色', '手机号码',
                    ['title' => '发卡时间', 'html' => $this->htmlDTRange('发卡时间')],
                    ['title' => '更新于', 'html' => $this->htmlDTRange('更新于')],
                    [
                        'title' => '状态 . 操作',
                        'html'  => $this->htmlSelect(
                            [null => '全部', 0 => '待发', 1 => '正常', 2 => '挂失'], 'filter_status'
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
                return $users->where('card_id', 0);
            }
            );
            $card = new Card;
            $sn = $card->input();
            // $sn = $card->input(
            //     (Request::route('id') ? false : true) and
            //     (Request::path() == 'cards/edit')
            // );
            $row = <<<HTML
                <tr>
                    <td>%s</td>
                    <td class="text-center">%s</td>
                    <td class="text-center">%s</td>
                    <td class="text-center">%s</td>
                    <td>$sn</td>%s
                </tr>
            HTML;
            $list = '';
            $i = 0;
            /** @var User $user */
            foreach ($users as $user) {
                $status = $action == 'create' ? ''
                    : '<td>' . $card->status($user->card ? $user->card->status : 1) . '</td>';
                $record = sprintf(
                    $row,
                    $user->id,
                    $user->realname,
                    $user->group->name,
                    $user->mobile,
                    $user->id, $i,
                    $user->card ? $user->card->sn : '',
                    $status
                );
                $list .= $record;
                $i++;
            }
            !empty($list)
                ?: $list = '<tr><td colspan="6" class="text-center text-red">- 已发卡 -</td></tr>';
            $data = [
                'list' => $list,
                'edit' => $action == 'edit' ? true : null,
            ];
        }
        $view->with($data);
    }
    
}