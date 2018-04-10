<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class OperatorPolicy {
    
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    public function create(User $user) {
        
        return in_array($user->group->name, Constant::SUPER_ROLES);
        
    }
    
    public function store(User $user) {
        
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            switch ($user->group->name) {
                case '运营':
                    return true;
                case '企业':
                    $group = Group::find(Request::input('group_id'));
                    return in_array($group->name, ['企业', '学校']);
                case '学校':
                    $group = Group::find(Request::input('group_id'));
                    return $group->name == '学校';
                default:
                    break;
            }
        }
        
        return false;
        
    }
    
    public function edit(User $user, User $operator) {
    
        return $this->operable($user, $operator->id);
        
    }
    
    public function update(User $user) {
        
        return $this->permit($user);
        
    }
    
    public function destroy(User $user) {
    
        return $this->permit($user);
        
    }
    
    /**
     * 检查权限
     *
     * @param User $user
     * @return bool
     */
    private function permit(User $user): bool {
      
        if (Request::has('ids')) {
            $ids = Request::input('ids');
            foreach ($ids as $id) {
                if (!$this->operable($user, $id)) { return false; }
            }
            return true;
        } else {
            $paths = explode('/', Request::path());
            $id = Request::input('id') || $paths[sizeof($paths) - 1];
            return $this->operable($user, $id);
        }
        
    }
    
    /**
     * 判断当前登录用户是否可以编辑/更新/删除指定的超级用户
     *
     * @param User $user
     * @param $id
     * @return bool
     */
    private function operable(User $user, $id): bool {
    
        $operator = User::find($id);
        abort_if(
            !$operator,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            switch ($user->group->name) {
                case '运营':
                    return true;
                case '企业':
                    return $operator->group->name != '运营';
                case '学校':
                    return $operator->group->name == '学校';
                default:
                    break;
            }
        }
        
        return false;
        
    }
    
}
