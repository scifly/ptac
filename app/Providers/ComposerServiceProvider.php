<?php
namespace App\Providers;

use Doctrine\Common\Inflector\Inflector;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Class ComposerServiceProvider
 * @package App\Providers
 */
class ComposerServiceProvider extends ServiceProvider {
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        
        $composers = [
            'init'           => ['index'],
            'action'         => ['index', 'create_edit'],
            'app'            => ['index', 'create', 'edit'],
            'article'        => ['index', 'create_edit'],
            'bed'            => ['index', 'create_edit'],
            'building'       => ['index'],
            'camera'         => ['index'],
            'card'           => ['index', 'create_edit'],
            'class'          => ['index', 'create_edit'],
            'column'         => ['index', 'create_edit'],
            'combo_type'     => ['index'],
            'company'        => ['index'],
            'conference'     => ['index', 'create_edit', 'edit'],
            'consumption'    => ['index', 'show'],
            'corp'           => ['index', 'create_edit', 'recharge'],
            'custodian'      => ['index', 'create', 'edit', 'issue', 'grant', 'face'],
            'department'     => ['create_edit'],
            'educator'       => ['index', 'create', 'edit', 'issue', 'grant', 'face', 'recharge'],
            'evaluate'       => ['index', 'create_edit'],
            'event'          => ['index', 'show'],
            'exam'           => ['index', 'create_edit', 'show'],
            'exam_type'      => ['index'],
            'face'           => ['index', 'create'],
            'flow_type'      => ['index', 'create_edit'],
            'flow'           => ['index', 'create', 'edit'],
            'grade'          => ['index', 'create_edit'],
            'group'          => ['index', 'create_edit', 'create', 'edit'],
            'icon'           => ['index', 'create_edit'],
            'indicator'      => ['index'],
            'major'          => ['index', 'create_edit'],
            'menu'           => ['create_edit', 'sort'],
            'message'        => ['index'],
            'message_type'   => ['index'],
            'module'         => ['index', 'create_edit'],
            'operator'       => ['index', 'create_edit'],
            'participant'    => ['index'],
            'passage_log'    => ['index'],
            'passage_rule'   => ['index', 'create_edit'],
            'partner'        => ['index', 'create_edit', 'recharge'],
            'poll'           => ['index', 'create_edit'],
            'poll_topic'     => ['index', 'create_edit'],
            'prize'          => ['index'],
            'room'           => ['index', 'create_edit'],
            'room_type'      => ['index', 'create_edit'],
            'school'         => ['index', 'create_edit', 'recharge'],
            'school_type'    => ['index'],
            'score'          => ['index', 'create_edit', 'stat'],
            'score_total'    => ['index'],
            'score_range'    => ['index', 'create_edit', 'stat'],
            'semester'       => ['index', 'create_edit'],
            'student'        => ['index', 'create', 'edit', 'issue', 'grant', 'face'],
            'subject'        => ['index', 'create_edit'],
            'subject_module' => ['index', 'create_edit'],
            'tab'            => ['index', 'create_edit'],
            'tag'            => ['index', 'create_edit'],
            'template'       => ['index', 'config'],
            'turnstile'      => ['index'],
            'user'           => ['edit', 'reset', 'message'],
            'wap'            => ['create_edit'],
        ];
        $wComposers = [
            'mobile' => ['index', 'module', 'article'],
            'mark'   => ['analyze', 'squad', 'stat', 'student'],
            'info'   => ['index', 'create_edit', 'show'],
        ];
        array_map(
            function ($type, $composers) {
                foreach ($composers as $dir => $views) {
                    $paths = array_map(
                        function ($view) use ($dir, $type) {
                            $path = empty($type) ? [$dir, $view] : [$type, $dir, $view];
                            
                            return join('.', $path);
                        }, $views
                    );
                    $dir = $dir != 'class' ? Inflector::camelize($dir) : 'Squad';
                    View::composer(
                        $paths, 'App\Http\ViewComposers\\' . ucfirst($dir) . 'Composer'
                    );
                }
            }, ['', 'wechat'], [$composers, $wComposers]
        );
        
    }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() { }
    
}
