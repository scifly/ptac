<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\App;
use App\Models\Corp;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AppIndexComposer {
    
    use ModelTrait;
    
    protected $corp;
    
    function __construct(Corp $corp) { $this->corp = $corp; }
    
    public function compose(View $view) {
        
        $user = Auth::user();
        if ($user->group->name === '运营') {
            $corps = Corp::all()->toArray();
            $apps = App::whereCorpId($corps)->get()->toArray();
            $view->with([
                'corps' => Corp::all()->pluck('name', 'id')->toArray(),
                'apps' => $apps,
                'uris' => $this->uris()
            ]);
        } else {
            $corpId = $this->corp->corpId();
            $corp = Corp::find($corpId);
            $apps = App::whereCorpId($corpId)->get()->toArray();
            $this->formatDateTime($apps);
            $view->with([
                'corp' => $corp,
                'apps' => $apps,
                'uris' => $this->uris(),
            ]);
        }
        
    }
    
    private function formatDateTime(&$apps) {
        
        Carbon::setLocale('zh');
        for ($i = 0; $i < sizeof($apps); $i++) {
            if ($apps[$i]['created_at']) {
                $dt = Carbon::createFromFormat('Y-m-d H:i:s', $apps[$i]['created_at']);
                $apps[$i]['created_at'] = $dt->diffForhumans();
            }
            if ($apps[$i]['updated_at']) {
                $dt = Carbon::createFromFormat('Y-m-d H:i:s', $apps[$i]['updated_at']);
                $apps[$i]['updated_at'] = $dt->diffForhumans();
            }
            
        }
        
    }
    
}
