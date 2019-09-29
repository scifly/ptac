<?php

namespace App\Policies;

use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\{RoomType, User};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class RoomTypePolicy
 * @package App\Policies
 */
class RoomTypePolicy {

    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Determine whether the user can (e)dit/(u)pdate/sync (m)enu of the app
     *
     * @param User $user
     * @param RoomType|null $rt
     * @return bool
     */
    function operation(User $user, RoomType $rt = null) {

        [$corpId, $ids] = array_map(
            function ($field) use ($rt) {
                return $this->field($field, $rt);
            }, ['corp_id', 'ids']
        );
        !$corpId ?: $perm = $corpId == $this->corpId();
        !$ids ?: $perm = RoomType::whereCorpId($this->corpId())
            ->pluck('id')->flip()->has(array_values($ids));

        return in_array($user->role(), ['运营', '企业']) && ($perm ?? true);
    
    }

}
