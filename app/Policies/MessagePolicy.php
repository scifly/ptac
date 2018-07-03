<?php

namespace App\Policies;


use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class MessagePolicy
 * @package App\Policies
 */
class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
}
