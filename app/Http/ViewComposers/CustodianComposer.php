<?php
namespace App\Http\ViewComposers;

use App\Models\Custodian;
use App\Models\User;
use Illuminate\Contracts\View\View;

class CustodianComposer{

    protected $user;

    public function __construct(User $user) {
        $this->user = $user;

    }

    public function compose(View $view)
    {
        $custodians =Custodian::with('user')->get()->toArray();

        if(!empty($custodians))
        {
            foreach ($custodians as $k=>$v)
            {
                $custodianName[$v['id']] = $v['user']['realname'];
            }
        }
        $view->with(['custodianName' => $custodianName]);
    }
}