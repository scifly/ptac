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
class CardCreateComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {

        $card = new Card;
        $userIds = Request::get('extra');
        $users = User::whereIn('id', $userIds)->where('card_id', null)->get();
        $list = '';
        $snHtml = $card->input();
        $row = <<<HTML
<tr>
<td class="valign">%s</td>
<td class="text-center valign">%s</td>
<td class="text-center valign">%s</td>
<td>$snHtml</td>
</tr>
HTML;
        $i = 0;
        foreach ($users as $user) {
            $list .= sprintf(
                $row,
                $user->id,
                $user->realname,
                $user->group->name,
                $user->id,
                $i,
                ''
            );
            $i++;
        }
        $view->with(['list' => $list]);
        
    }
    
}