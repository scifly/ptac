<?php
namespace App\Http\ViewComposers;

use App\Models\Custodian;
use App\Models\Group;
use App\Models\School;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class StudentComposer {
    
    protected $user, $class, $custodian, $group;
    
    public function __construct(User $user, Squad $class, Group $group, Custodian $custodian) {
        
        $this->user = $user;
        $this->class = $class;
        $this->group = $group;
        $this->custodian = $custodian;
        
    }
    
    public function compose(View $view) {

        $user = Auth::user();
        $schools = null;
        switch ($user->group->name) {
            case '运营':
                $schools = School::whereEnabled(1)->pluck('name', 'id');
                break;
            
        }
        $Custodian = Custodian::with('user')->get()->toArray();
        if (!empty($Custodian)) {
            foreach ($Custodian as $k => $v) {
                $custodian[$v['id']] = $v['user']['realname'];
            }
        }
        $view->with([
            'user'       => $this->user->pluck('realname', 'id'),
            'class'      => $this->class->pluck('name', 'id'),
            'groups'     => $this->group->pluck('name', 'id'),
            'custodians' => $custodian,
        ]);
        
    }
    
}