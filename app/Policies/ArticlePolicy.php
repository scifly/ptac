<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait, PolicyTrait};
use App\Models\{User, Wap, Column, Article};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ArticlePolicy
 * @package App\Policies
 */
class ArticlePolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
    /**
     * @param User $user
     * @param Article|null $article
     * @return bool
     */
    function operation(User $user, Article $article = null) {
    
        $perm = true;
        $schoolIds = $this->schoolIds()->flip();
        [$columnId, $ids] = array_map(
            function ($field) use ($article) {
                return $this->field($field, $article);
            }, ['column_id', 'ids']
        );
        !$columnId ?: $perm &= $schoolIds->has(
            Column::find($columnId)->wap->school_id
        );
        !$ids ?: $perm &= Wap::whereSchoolId($this->schoolId())
            ->articles->pluck('id')->flip()->has(array_values($ids));
    
        return in_array($user->role(), Constant::SUPER_ROLES) && ($perm ?? true);
        
    }
    
}
