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
            'init'                   => ['index'],
            'action'                 => ['index', 'create_edit'],
            'app'                    => ['index'],
            'turnstile'              => ['index'],
            'card'                   => ['index', 'create_edit'],
            'combo_type'             => ['index'],
            'company'                => ['index'],
            'conference_participant' => ['index'],
            'conference_queue'       => ['index', 'create_edit', 'edit'],
            'conference_room'        => ['index'],
            'consumption'            => ['index', 'show'],
            'corp'                   => ['index', 'create_edit'],
            'custodian'              => ['index', 'create', 'edit', 'issue', 'grant'],
            'department'             => ['create_edit'],
            'educator'               => ['index', 'create', 'edit', 'issue', 'grant'],
            'event'                  => ['index', 'show'],
            'exam'                   => ['index', 'create_edit', 'show'],
            'exam_type'              => ['index'],
            'grade'                  => ['index', 'create_edit'],
            'group'                  => ['index', 'create_edit', 'create', 'edit'],
            'icon'                   => ['index', 'create_edit'],
            'major'                  => ['index', 'create_edit'],
            'menu'                   => ['create_edit', 'sort'],
            'message'                => ['index', 'create_edit'],
            'message_type'           => ['index'],
            'module'                 => ['index', 'create_edit'],
            'operator'               => ['index', 'create_edit'],
            'passage_log'            => ['index'],
            'passage_rule'           => ['index', 'create_edit'],
            'partner'                => ['index'],
            'pq_choice'              => ['index', 'create_edit'],
            'pq_subject'             => ['index', 'create_edit'],
            'procedure'              => ['index', 'create_edit'],
            'procedure_step'         => ['index', 'create_edit'],
            'procedure_type'         => ['index'],
            'school'                 => ['index', 'create_edit'],
            'school_type'            => ['index'],
            'score'                  => ['index', 'create_edit', 'stat'],
            'score_total'            => ['index'],
            'score_range'            => ['index', 'create_edit', 'stat'],
            'semester'               => ['index', 'create_edit'],
            'class'                  => ['index', 'create_edit'],
            'student'                => ['index', 'create', 'edit', 'issue', 'grant'],
            'subject'                => ['index', 'create_edit'],
            'subject_module'         => ['index', 'create_edit'],
            'tab'                    => ['index', 'create_edit'],
            'tag'                    => ['index', 'create_edit'],
            'user'                   => ['edit', 'reset', 'message', 'event'],
            'wap_site'               => ['create_edit'],
            'wap_site_module'        => ['index', 'create_edit'],
            'wsm_article'            => ['index', 'create_edit'],
        ];
        $wComposers = [
            'mobile_site' => ['index', 'module', 'article'],
            'score_center'   => ['analyze', 'squad', 'stat', 'student'],
            'message_center' => ['index', 'create_edit', 'show'],
        ];
        array_map(
            function ($type, $composers) {
                foreach ($composers as $dir => $views) {
                    $paths = array_map(
                        function ($view) use ($dir, $type) {
                            $path = empty($type) ? [$dir, $view] : [$type, $dir, $view];
                            
                            return implode('.', $path);
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
