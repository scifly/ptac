<?php
namespace App\Http\Controllers;

use App\Http\Requests\AppRequest;
use App\Models\App;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

/**
 * 微信企业应用
 *
 * Class AppController
 * @package App\Http\Controllers
 */
class AppController extends Controller {
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);

    }
    
    /**
     * 微信应用列表
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function index() {

        if (Request::method() == 'POST') {
            return App::store();
        }
        
        return $this->output();
        
    }
    
    /**
     * 编辑微信应用
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $app = App::find($id);
        $this->authorize('eum', $app);

        return $this->output(['app' => $app]);
        
    }

    /**
     * 更新微信应用
     *
     * @param AppRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(AppRequest $request, $id) {
        
        $app = App::find($id);
        $this->authorize('eum', $app);

        return $this->result($app->update($request->all()));

    }
    
    /**
     * 获取指定应用的menu
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function menu($id) {
        
        $app = App::find($id);
        $this->authorize('eum', $app);

        $menu = "[
            {
                \"name\": \"\u6d4b\u8bd5\",
                \"sub_button\": [
                    {
                        \"type\": \"view\",
                        \"name\": \"\u968f\u4fbf\",
                        \"key\": \"https:\/\/www.baidu.com\",
                        \"sub_button\": [
                        
                        ],
                        \"url\": \"https:\/\/www.baidu.com\"
                    },
                    {
                        \"type\": \"view\",
                        \"name\": \"\u6d4b\u8bd5\",
                        \"key\": \"https:\/\/www.baidu.com\",
                        \"sub_button\": [
                        
                        ],
                        \"url\": \"https:\/\/www.baidu.com\"
                    }
                ]
            },
            {
                \"name\": \"\u6d4b\u8bd5\",
                \"sub_button\": [
                    {
                        \"type\": \"view\",
                        \"name\": \"\u767e\u5ea6\",
                        \"key\": \"http:\/\/www.baidu.com\",
                        \"sub_button\": [
                        
                        ],
                        \"url\": \"http:\/\/www.baidu.com\"
                    }
                ]
            },
            {
                \"name\": \"\u6d4b\u8bd5\",
                \"sub_button\": [
                    {
                        \"type\": \"view\",
                        \"name\": \"\u767e\u5ea6\",
                        \"key\": \"http:\/\/www.baidu.com\",
                        \"sub_button\": [
                        
                        ],
                        \"url\": \"http:\/\/www.baidu.com\"
                    }
                ]
            }
        ]";

        return $this->output(['menu' => json_decode($menu)]);

    }

}
