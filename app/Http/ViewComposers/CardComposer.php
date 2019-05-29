<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Card;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class CustodianIssueComposer
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
                    [
                        'title' => '发卡时间',
                        'html'  => $this->inputDateTimeRange('发卡时间'),
                    ],
                    [
                        'title' => '更新于',
                        'html'  => $this->inputDateTimeRange('更新于'),
                    ],
                    [
                        'title' => '状态 . 操作',
                        'html'  => $this->singleSelectList(
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
            $isBatch = Request::route('id') ? false : true;
            if ($ids) {
                session(['ids' => $ids]);
            } else {
                $ids = session('ids');
            }
            $operator = $action == 'create' ? '=' : '<>';
            $users = User::whereIn('id', $ids)->where('card_id', $operator, 0)->get();
            $card = new Card;
            $sn = $card->input($isBatch and (Request::path() == 'cards/edit'));
            $row = <<<HTML
<tr>
<td>%s</td>
<td class="text-center">%s</td>
<td class="text-center">%s</td>
<td class="text-center">%s</td>
<td>$sn</td>%s
</tr>
HTML;
            $list = ''; $i = 0;
            /** @var User $user */
            foreach ($users as $user) {
                $status = $action == 'create' ? ''
                    : '<td>' . $card->status($user->card->status) . '</td>';
                $default = $user->mobiles->where('isdefault', 1)->first();
                $record = sprintf(
                    $row,
                    $user->id,
                    $user->realname,
                    $user->group->name,
                    $default ? $default->mobile : 'n/a',
                    $user->id, $i,
                    $user->card ? $user->card->sn : '',
                    $status
                );
        
                $list .= $record;
                $i++;
            }
            !empty($list)
                ?: $list = '<tr><td colspan="6" class="text-center text-red">- 请先发卡 -</td></tr>';
            $data = [
                'list' => $list,
                'edit' => $action == 'edit' ? true : null
            ];
        }
        
        $view->with($data);
    }
    
}