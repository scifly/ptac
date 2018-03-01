<?php

namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\ConferenceQueue;
use App\Models\ConferenceRoom;
use App\Models\Corp;
use App\Models\Educator;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class EducatorPolicy {

    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }

    public function c(User $user) {



    }


}
