<div id="main">

    <div class="me_normalCntHead msg_normalCntHead">
        <div class="me_normalCntHead_title">
            <div class="me_normalCntHead_title_content">
                <ul class="me_btnGroup">
                    <li><a class="qui_btn me_btn me_btn_title head_btn me_btn_Active" href="#createMessage">发消息</a></li>
                    <li><a class="qui_btn me_btn me_btn_title head_btn" href="#messageList">已发送</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="me_tab">
        <!--发消息-->
        <div id="createMessage" class="tab_con tab_con_Active"  style="padding: 18px 100px 24px;">
            <div class="msg_infoItem msg_create_infoItem msg_create_infoItem_Apps msg_infoItem_Init js_select_wrap">
                <div class="msg_infoItem_infoInit" id="chooseapp">
                    <a class="msg_create_infoItem_selectApps js_select_apps_btn" href="javascript:;" data-toggle="modal" data-target="#modal-app">选择需要发消息的应用</a>
                </div>
                <div class="msg_infoItem_infoDone" id="app_list" style="display: none;">
                    <div class="msg_create_infoItem_title">应用名称:</div>
                    <div class="me_groupSelBtn me_groupSelBtn_ImageNoPadding js_select_appList" id="app_select_list">
                        <div class="me_groupSelBtn_item js_app_group_item" data-business_id="5629500423222674">
                            <img src="//wx.qlogo.cn/mmhead/Q3auHgzwzM59L5XCGbLiaHTIr3UbicFia81iaeMpUSkic1BzHauETqm3Nrg/0" class="me_groupSelBtn_icon">
                            <span class="me_groupSelBtn_item_text">企业小助手</span>
                        </div>
                        <a class="me_groupSelBtn_add js_edit_app_dlg" href="javascript:;" data-toggle="modal" data-target="#modal-app">修改</a>
                    </div>
                </div>
            </div>

            <div class="msg_infoItem msg_create_infoItem msg_create_infoItem_Scope js_range_wrap msg_infoItem_Init msg-disabled">
                <div class="msg_infoItem_infoInit" id="chooserange" >
                    <a class="msg_create_infoItem_selectScope js_select_range_btn" href="javascript:;" data-toggle="modal" data-target="#modal-range">选择发送范围</a>
                </div>
                <div class="msg_infoItem_infoDone" id="range_list" style="display: none;">
                    <div class="msg_create_infoItem_title">发送范围:</div>
                    <div class="me_groupSelBtn js_range_list" id="js_range_list">

                    </div>
                </div>
            </div>

            <div class="msg_infoItem msg_create_infoItem msg_create_infoItem_Editor msg_create_customBlockElem js_message_editor msg-disabled">
                <div class="msg_create_msgPanel js_msg_type_wrap msg_create_msgPanel_Text" target-type="msg_create_msgPanel_Text">
                    <div class="msg_create_msgPanel_header">
                        <ul class="msg_create_msgPanel_list">
                            <li class="msg_create_msgPanel_item js_msg_type_btn js_msg_type_word" msg-type="text" target-type="msg_create_msgPanel_Text">
                                <span class="me_icon me_icon_GrayText"></span>
                                <span class="msg_create_msgPanel_item_title msg_create_msgPanel_item_title_Text">文字</span>
                            </li>
                            <li class="msg_create_msgPanel_item js_msg_type_btn js_msg_type_mpNews js_msg_type_dropdown me_btnWithMenu" msg-type="mpnews" target-type="msg_create_msgPanel_MpNews">
                                <span class="me_icon me_icon_GrayMpNews"></span>
                                <span class="msg_create_msgPanel_item_title msg_create_msgPanel_item_title_MpNews js_msg_type_dropdown_label">图文</span>
                            </li>
                            <div style="clear: both"></div>
                        </ul>
                    </div>

                    <div class="msg_create_msgPanel_cnt msg_create_msgPanel_cntText type-cur" id="msg-text" data-type="text">
                        <textarea maxlength="600" class="msg_create_msgPanel_cnt_text js_send_msg_text msg-content" placeholder="直接开始输入..."></textarea>
                    </div>

                    <div class="msg_create_msgPanel_cnt msg_create_msgPanel_cntMpNews" id="msg-mpnews"  style="display: none;" data-type="mpnews">
                        <a class="msg_create_msgPanel_cnt_addBtn js_create_mpNews_btn" href="javascript:;" data-toggle="modal" data-target="#modal-Mpnews" >
                            <div class="msg_create_msgPanel_cnt_cross"></div>
                            <span href="javascript:;" class="msg_create_msgPanel_cnt_addBtnText">添加图文</span>
                        </a>
                        <div class="msg_create_msgPanel_cnt_showMpNews js_amrd_mpnews_wrap msg-content" style="display: none" data-toggle="modal" data-target="#modal-Mpnews" >

                        </div>
                    </div>

                </div>
            </div>

            <div class="msg_infoItem msg_create_infoItem msg_create_infoItem_Last msg-disabled" id="msg-send-btns" >
                <a class="btn-primary qui_btn ww_btn btn-blue" href="javascript:;">发送</a>
                <a class="btn-default qui_btn ww_btn " href="javascript:;">定时发送</a>
                <a class="btn-default qui_btn ww_btn " href="javascript:;" id="browse" data-toggle="modal" data-target="#modal-browse">预览</a>
            </div>

        </div>

        <!--历史消息-->
        <div id="messageList" class="tab_con">
            <div class="qui_tab ww_tab">
                <div class="qui_tab_title ww_tab_title">
                    <ul class="qui_tabNav ww_tabNav js_filterapp_wrap">

                        <li class="qui_tabNav_item ww_tabNav_item js_filterapp_item ww_tabNav_item_Curr" appid="0">
                            <div class="qui_tabNav_itemLink ww_tabNav_itemLink">
                                <div class="msg_appNameWithLogo">
                                    <div class="msg_appNameWithLogo_logo">
                                        <span class="ww_icon ww_icon_AppAll"></span>
                                    </div>
                                    <span class="msg_appNameWithLogo_name">全部消息</span>
                                </div>
                            </div>
                        </li>

                        <li class="qui_tabNav_item ww_tabNav_item  js_filterapp_item" appid="5629500423222674">
                            <div class="qui_tabNav_itemLink ww_tabNav_itemLink">
                                <div class="msg_appNameWithLogo">
                                    <div class="msg_appNameWithLogo_logo">
                                        <img src="//wx.qlogo.cn/mmhead/Q3auHgzwzM59L5XCGbLiaHTIr3UbicFia81iaeMpUSkic1BzHauETqm3Nrg/0" class="ww_iconWidthOutline">
                                    </div>
                                    <span class="msg_appNameWithLogo_name" style="width: 100px">企业小助手</span>
                                </div>
                            </div>
                        </li>

                        <li class="qui_tabNav_item ww_tabNav_item  js_filterapp_item" appid="5629500423222674">
                            <div class="qui_tabNav_itemLink ww_tabNav_itemLink">
                                <div class="msg_appNameWithLogo">
                                    <div class="msg_appNameWithLogo_logo">
                                        <img src="//wx.qlogo.cn/mmhead/Q3auHgzwzM59L5XCGbLiaHTIr3UbicFia81iaeMpUSkic1BzHauETqm3Nrg/0" class="ww_iconWidthOutline">
                                    </div>
                                    <span class="msg_appNameWithLogo_name" style="width: 100px">企业小助手</span>
                                </div>
                            </div>
                        </li>

                    </ul>
                </div>

                <div class="qui_tab_cnt ww_tab_cnt">
                    <div class="ww_tab_cnt_inside">
                        <div class="msg_history_cnt">
                            <div id="messageListViewTable">
                                <table class="ww_table ww_table_TextEllipsis msg_history_msgList">
                                    <thead>
                                    <tr>
                                        <th class="msg_history_msgList_th msg_history_msgList_th_Status" width="30">
                                            <div>状态</div>
                                        </th>
                                        <th class="msg_history_msgList_th msg_history_msgList_th_Type">
                                            <div>消息类型</div>
                                        </th>
                                        <th class="msg_history_msgList_th msg_history_msgList_th_Text">
                                            <div>消息主题</div>
                                        </th>
                                        <th class="msg_history_msgList_th msg_history_msgList_th_Date">
                                            <div>时间</div>
                                        </th>
                                        <th class="msg_history_msgList_th msg_history_msgList_th_Operation">
                                            <div>操作</div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="messageListContent">
                                    <tr msg-status="draft" appid="5629502790236481" msg-type="mpnews" msgid="48611" class="msg_history_msgList_tr js_msg_row">
                                        <td class="msg_history_msgList_td msg_history_msgList_th_Status">
                                            <i node-type="icon" class="ww_icon ww_icon_NoticeStatusDraft"></i>
                                            <span class="" node-type="statusText">已发送</span>
                                        </td>
                                        <td class="msg_history_msgList_td">  图文  </td>
                                        <td class="msg_history_msgList_td">   1  </td>
                                        <td class="msg_history_msgList_td msg_history_msgList_td_Date">  2017-12-15  </td>
                                        <td class="msg_history_msgList_td msg_history_msgList_td_Operation js_row_operationRow  " node-type="operationItem">
                                            <!--<a href="#message/5629502790236481/48611" class="js_edit_message">编辑</a>-->
                                            <span></span><a msgid="48611" class="js_delete_message" href="javascript:;">删除</a>
                                        </td>
                                        <td class="msg_history_msgList_td msg_history_msgList_td_Filter">  </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>

</div>