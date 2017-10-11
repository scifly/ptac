<div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-title">
                    <a href="#" class="btn btn-primary">
                        <i class="fa fa-mail-reply"></i> 返回
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="app_menu">
                    <div class="row no-margin">
                        <div class="menu_left_panel col-sm-7">
                            <!--这里显示-->
                            <ul class="show_list" id="showList">
                                <li class="show_item">
                                    <dl class="show_item_cnt">
                                        <dt class="show_item_cnt_title">菜单1</dt>
                                        <dd class="show_item_cnt_menuName show_item_cnt_menuName_first">
                                            测试1
                                        </dd>
                                        <dd class="show_item_cnt_menuName">测试2</dd>
                                        <a href="javascript:"
                                           class="show_item_cnt_editBtn js_enter_menuEdit">
                                            <span class="fa fa-edit"></span>
                                        </a>
                                    </dl>
                                </li>
                                <li class="show_item">
                                    <dl class="show_item_cnt">
                                        <dt class="show_item_cnt_title">菜单1</dt>
                                        <dd class="show_item_cnt_menuName show_item_cnt_menuName_first">
                                            测试1
                                        </dd>
                                        <dd class="show_item_cnt_menuName">测试2</dd>
                                        <a href="javascript:"
                                           class="show_item_cnt_editBtn js_enter_menuEdit">
                                            <span class="fa fa-edit"></span>
                                        </a>
                                    </dl>
                                </li>
                                <li class="show_item">
                                    <dl class="show_item_cnt">
                                        <dt class="show_item_cnt_title">菜单1</dt>
                                        <dd class="show_item_cnt_menuName show_item_cnt_menuName_first">
                                            测试1
                                        </dd>
                                        <dd class="show_item_cnt_menuName">测试2</dd>
                                        <a href="javascript:"
                                           class="show_item_cnt_editBtn js_enter_menuEdit">
                                            <span class="fa fa-edit"></span>
                                        </a>
                                    </dl>
                                </li>
                                <li class="show_item show_item_addMenu">
                                    <a href="javascript:" class="add_menu_btn" id="addMenu">添加主菜单</a>
                                </li>
                                <li class="show_item show_item_publish">
                                    <a href="javascript:" class="btn btn-primary">保存</a>
                                    <span class="publish_note">应用菜单更新需保存后才能生效</span>
                                </li>
                            </ul>
                            <!--页面刚开始加载的时候这里为空-->
                            <div class="edit_menu_container" id="editMenuContainer" style="display: none">
                                <form class="edit_form">
                                    <!--主菜单-->
                                    <div class="editMenu_header">
                                        <span class="editMenu_header_title">主菜单</span>
                                        <span class="editMenu_tips">请输入主菜单名字</span>
                                        <input type="text" class="edit_text">
                                        <span class="del_menu fa fa-trash-o"></span>
                                    </div>
                                    <!--子菜单-->
                                    <div class="editMenu_body" id="editMenuBody">
                                        <!--主菜单下的子菜单-->
                                        <div class="main_menu_container">
                                            <div class="menuInfoPanel">
                                                <div class="menuInfoPanel_body">
                                                    <ul class="menuInfoPanel_list">
                                                        <li class="menuInfoPanel_item">
                                                            <div class="menuInfoPanel_item_title">
                                                                菜单内容：
                                                            </div>
                                                            <!--改为默认一个值-->
                                                            <span class="menuInfoPanel_item_type">跳转到网页</span>
                                                            <input type="hidden" name="type" value="view">
                                                        </li>
                                                        <li class="menuInfoPanel_item menuInfoPanel_jumpType">
                                                            <div class="menuInfoPanel_item_title">
                                                                网址：
                                                            </div>
                                                            <div class="menuInfoPanel_item_content">
                                                                <input type="text" name="url"
                                                                       class="menuInfoPanel_item_url">
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <!--子菜单-->
                                        <!--<div class="subMenu_container">-->
                                        <!--<div class="menuInfoPanel">-->
                                        <!--<div class="editMenu_header">-->
                                        <!--<span class="editMenu_header_title">子菜单</span>-->
                                        <!--<span class="editMenu_tips">请输入主菜单名字</span>-->
                                        <!--<input type="text" class="edit_text">-->
                                        <!--<span class="del_menu fa fa-trash-o"></span>-->
                                        <!--</div>-->
                                        <!--<div class="menuInfoPanel_body">-->
                                        <!--<ul class="menuInfoPanel_list">-->
                                        <!--<li class="menuInfoPanel_item">-->
                                        <!--<div class="menuInfoPanel_item_title">-->
                                        <!--菜单内容：-->
                                        <!--</div>-->
                                        <!--&lt;!&ndash;改为默认一个值&ndash;&gt;-->
                                        <!--<span class="menuInfoPanel_item_type">跳转到网页</span>-->
                                        <!--<input type="hidden" name="type" value="view">-->
                                        <!--</li>-->
                                        <!--<li class="menuInfoPanel_item">-->
                                        <!--<div class="menuInfoPanel_item_title">-->
                                        <!--网址：-->
                                        <!--</div>-->
                                        <!--<div class="menuInfoPanel_item_content">-->
                                        <!--<input type="text" name="url"-->
                                        <!--class="menuInfoPanel_item_url">-->
                                        <!--</div>-->
                                        <!--</li>-->
                                        <!--</ul>-->
                                        <!--</div>-->
                                        <!--</div>-->
                                        <!--</div>-->
                                        <a href="javascript:" class="editMenu_addSubMenu"
                                           id="btnAddSubMenu">添加子菜单</a>
                                    </div>
                                    <div class="editMenu_footer">
                                        <a href="javascript:" class="btn btn-primary margin-r-5"
                                           id="btnEditMenuSave">保存</a>
                                        <a href="javascript:" class="btn btn-default"
                                           id="btnEditMenuCancel">取消</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!--right先不管-->
                        <div class="menu_right_panel col-sm-4 col-sm-offset-1">
                            <div class="preview_panel">
                                <div class="preview_panel_header">
                                    <h3>微网站</h3>
                                </div>
                                <div class="preview_panel_footerBar footerBar_menuCount3">
                                    <div class="footerBar_itemMenu">
                                        <a href="javascript:">测试1</a>
                                    </div>
                                    <div class="footerBar_itemMenu">
                                        <a href="javascript:">测试2</a>
                                    </div>
                                    <div class="footerBar_itemMenu">
                                        <a href="javascript:">测试3</a>
                                    </div>
                                    <ul class="previewPanel_subMenuWrap previewPanel_subMenuWrap_Menu1">
                                        <li class="previewPanel_subMenu">
                                            <a href="javascript:">青蛙</a>
                                        </li>
                                        <li class="previewPanel_subMenu">
                                            <a href="javascript:">兔子</a>
                                        </li>
                                    </ul>
                                    <ul class="previewPanel_subMenuWrap previewPanel_subMenuWrap_Menu2">
                                        <li class="previewPanel_subMenu">
                                            <a href="javascript:">青蛙</a>
                                        </li>
                                        <li class="previewPanel_subMenu">
                                            <a href="javascript:">兔子</a>
                                        </li>
                                    </ul>
                                    <ul class="previewPanel_subMenuWrap previewPanel_subMenuWrap_Menu3">
                                        <li class="previewPanel_subMenu">
                                            <a href="javascript:">青蛙</a>
                                        </li>
                                        <li class="previewPanel_subMenu">
                                            <a href="javascript:">兔子</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script></script>