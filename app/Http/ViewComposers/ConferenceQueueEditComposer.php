<?php
namespace App\Http\ViewComposers;

use App\Models\ConferenceQueue;
use App\Models\ConferenceRoom;
use App\Models\Educator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class ConferenceQueueEditComposer {

    protected $conferenceRoom, $educator;

    public function __construct(ConferenceRoom $conferenceRoom, Educator $educator) {

        $this->conferenceRoom = $conferenceRoom;
        $this->educator = $educator;

    }

    public function compose(View $view) {

        $view->with([
            'selectedEducators' => $this->educator->getEducatorListByIds(
                ConferenceQueue::find(Request::route('id'))->educator_ids
            )
        ]);

    }

}