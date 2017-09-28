<?php
namespace App\Http\ViewComposers;

use App\Models\Educator;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Contracts\View\View;

class EventComposer {

    protected $educators;
    protected $subjects;
    protected $user;
    protected $userId;

    public function __construct(Educator $educator, User $user, Subject $subject) {
        $this->educators = $educator;
        $this->subjects = $subject;
        $this->user = $user;
        //$this->userId = Session::get('user');
        $this->userId = 1;
    }

    public function compose(View $view) {

//            var_dump( $this->getSubjects($this->userId));
//            echo '*********************';
//            var_dump( $this->getEducators($this->userId));
//            die();
        $view->with([
            'educators' => $this->getEducators($this->userId),
            'subjects'  => $this->getSubjects($this->userId),
        ]);
    }

    private function getEducators($userId) {
        $educator = Educator::where('user_id', $userId)->first();
        $data = Educator::with('user')->where('school_id', $educator->school_id)->get()->toArray();
        $educatorArr = [];
        foreach ($data as $v) {
            $educatorArr[$v['id']] = $v['user']['realname'];
        }
        $educatorArr[0] = "无";

        return $educatorArr;
    }

    private function getSubjects($userId) {
        $educator = Educator::where('user_id', $userId)->first();
        $subjects = Subject::where('school_id', $educator->school_id)->pluck('name', 'id');
        $subjects[0] = "无";

        return $subjects;
    }
}