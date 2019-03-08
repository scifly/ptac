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

        $action = Request::route()->getActionMethod();
        $ids = Request::route('id') ? [Request::route('id')] : Request::get('ids');
        if ($ids) {
            session(['ids' => $ids]);
        } else {
            $ids = session('ids');
        }
        $operator = $action == 'create' ? '=' : '<>';
        $users = User::whereIn('id', $ids)->where('card_id', $operator, 0)->get();
        $card = new Card;
        $sn = $card->input();
        $row = <<<HTML
<tr>
<td>%s</td>
<td class="text-center">%s</td>
<td class="text-center">%s</td>
<td class="text-center">%s</td>
<td>$sn</td>%s
</tr>
HTML;
        $status = $action == 'create' ? '' : '<td>' . $card->status() . '</td>';
        $list = ''; $i = 0;
        /** @var User $user */
        foreach ($users as $user) {
            $record = sprintf(
                $row,
                $user->id,
                $user->realname,
                $user->group->name,
                $user->mobiles->where('isdefault', 1)->first()->mobile,
                $user->id, $i,
                $user->card ? $user->card->sn : '',
                $status
            );
            $action == 'create' ?: $record = sprintf($record, $user->card->status);
            $list .= $record;
            $i++;
        }
        !empty($list)
            ?: $list = '<tr><td colspan="6" class="text-center">- 请先发卡 -</td></tr>';
        $view->with([
            'list' => $list,
            'edit' => $action == 'edit' ? true : null
        ]);
        
    }
    
}