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
        $userIds = Request::route('id') ? [Request::route('id')] : array_values(Request::get('ids'));
        $operator = $action == 'create' ? '=' : '<>';
        $users = User::whereIn('id', $userIds)->where('card_id', $operator, 0)->get();
        $card = new Card;
        $sn = $card->input();
        $row = <<<HTML
<tr>
<td class="valign">%s</td>
<td class="text-center valign">%s</td>
<td class="text-center valign">%s</td>
<td class="text-center valign">%s</td>
<td class="valign">$sn</td>%s
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
        $view->with([
            'list' => $list,
            'edit' => $action == 'edit' ? true : null
        ]);
        
    }
    
}