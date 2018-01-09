<?php
namespace App\Listeners;

use App\Models\WapSite;
use Illuminate\Events\Dispatcher;

class WapSiteEventSubscriber {
    
    protected $wapSite;
    
    function __construct(WapSite $wapSite) {
        $this->wapSite = $wapSite;
    }
    
    /**
     * 当学校创建成功时，创建对应的微网站
     * @param $event
     * @return bool
     */
    public function onSchoolCreated($event) {
        $school = $event->school;
        $wapSiteData = [
            'school_id'  => $school->id,
            'site_title' => $school->name . '微网站',
            'media_ids'  => '0',
            'enabled'    => 1,
        ];
        return $this->wapSite->create($wapSiteData) ? true : false;
    }
    
    /**
     * 当删除学校时，删除对应的微网站
     * @param $event
     */
    public function onSchoolDeleted($event) {
        $school = $event->school;
        $site = $this->wapSite->where('school_id', $school->id)->first();
        //未真正的删除
        $site->enabled = 0;
        $site->save();
    }
    
    /**
     * Register the listeners for the subscriber
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events) {
        
        $e = 'App\\Events\\';
        $l = 'App\\Listeners\\WapSiteEventSubscriber@';
        $events->listen($e . 'SchoolCreated', $l . 'onSchoolCreated');
        $events->listen($e . 'SchoolDeleted', $l . 'onSchoolDeleted');
        
    }
    
}