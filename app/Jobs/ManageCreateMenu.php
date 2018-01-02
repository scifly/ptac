<?php
namespace App\Jobs;

use App\Models\Menu;
use App\Models\MenuTab;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ManageCreateMenu implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $parentMenu, $menuNameTab, $menu, $menuTab;
    
    /**
     * Create ent job instncent   *
     * @param $parentMenu
     */
    public function __construct($parentMenu) {
        $this->parentMenu = $parentMenu;
    }
    
    /**
     * Exeute the job.
     *
     * @return bool
     * @throws \Exception
     *u@throws \Throwable
     */
    public function handle() {
        // $this->menuNameTab = [
        //     '首页', '自媒体管理', '课程表管理', '用户中心',
        //     '通讯录', '成绩管理', '考勤管理', '移动办公', '系统设置'
        // ];
        $this->menu = new Menu();
        $this->menuNameTab = [];
        $schoolMenus = $this->menu->where('parent_id',5)->get();
        foreach ($schoolMenus as $schoolMenu){
            $this->menuNameTab[] = $schoolMenu->name;
        }
        return $this->storeSchoolMenu($this->parentMenu, $this->menuNameTab);
    }
    
    /**
     * @param $parentMenu
     * @param $menuNameTabs
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    private function storeSchoolMenu($parentMenu, $menuNameTabs) {
        # 创建新的Menu记录及卡片绑定记录
        try {
            DB::transaction(
                function () use ($parentMenu, $menuNameTabs) {
                    $this->menuTab = new MenuTab();
                    $this->menu = new Menu();
                    #查询 同名菜单名下 第一个菜单对应卡片的ids
                    foreach ($menuNameTabs as $menuNameTab) {
                        $tabIds = [];
                        $oldMenu = $this->menu->where('name', $menuNameTab)->first();
                        $uri = $oldMenu->uri;
                        $tabs = $oldMenu->tabs;
                        foreach ($tabs as $tab) {
                            $tabIds[] = $tab->id;
                        }
                        # 菜单下存在卡片
                        if (!empty($tabIds)) {
                            $menuData = [
                                'parent_id'    => $parentMenu->id,
                                'name'         => $menuNameTab,
                                'menu_type_id' => 5,
                                'position'     => $this->menu->all()->count(),
                                'enabled'      => 1,
                            ];
                            $m = $this->menu->create($menuData);
                            $this->menuTab->storeByMenuId($m->id, $tabIds);
                        }
                        #菜单下存在uri
                        else if (!empty($uri)) {
                            $menuData = [
                                'parent_id'    => $parentMenu->id,
                                'name'         => $menuNameTab,
                                'menu_type_id' => 5,
                                'position'     => $this->menu->all()->count(),
                                'uri'          => $uri,
                                'enabled'      => 1,
                            ];
                            $this->menu->create($menuData);
                        }
                        #存在下一级菜单
                        else {
                            $menuData = [
                                'parent_id'    => $parentMenu->id,
                                'name'         => $menuNameTab,
                                'menu_type_id' => 5,
                                'position'     => $this->menu->all()->count(),
                                'enabled'      => 1,
                            ];
                            $m = $this->menu->create($menuData);
                            $children = $this->menu->where('parent_id', $oldMenu->id)->get();
                            $childName = [];
                            foreach ($children as $child) {
                                $childName[] = $child->name;
                            }
                            if (!empty($childName)) {
                                #若子菜单和父菜单同名 会陷入死循环
                                $this->storeSchoolMenu($m, $childName);
                            }
                        }
                    }
                });
        
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
    }
}
