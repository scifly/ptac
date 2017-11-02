$(function () {
    var $addMenu = $("#addMenu");
    var $showList = $("#showList");
    var $editMenuContainer = $("#editMenuContainer");
    var $btnEditMenuCancel = $("#btnEditMenuCancel");
    var $btnEditMenuSave = $("#btnEditMenuSave");
    var $btnAddSubMenu = $("#btnAddSubMenu");
    var $editMenuBody = $("#editMenuBody");
    var $mainMenuContainer = $(".main_menu_container");
    var $subMenuContainer = $(".subMenu_container");
    var $delSubMenu = $(".del_subMenu");
    // 添加主菜单
    $addMenu.click(function () {
        $editMenuContainer.show();
        $showList.hide();
    });
    // 取消添加主菜单
    $btnEditMenuCancel.click(function () {
        $editMenuContainer.hide();
        $showList.show();
    });
    // 保存添加的主菜单
    $btnEditMenuSave.click(function () {
        $editMenuContainer.hide();
        $showList.show();
    });
    // 添加子菜单
    var subMenuHtml = '<div class="subMenu_container">' +
        '<div class="menuInfoPanel">' +
        '<div class="editMenu_header">' +
        '<span class="editMenu_header_title">子菜单</span>' +
        '<span class="editMenu_tips">请输入主菜单名字</span>' +
        '<input type="text" class="edit_text">' +
        '<span class="del_subMenu fa fa-trash-o"></span>' +
        '</div>' +
        '<div class="menuInfoPanel_body">' +
        '<ul class="menuInfoPanel_list">' +
        '<li class="menuInfoPanel_item">' +
        '<div class="menuInfoPanel_item_title">菜单内容：</div>' +
        '<span class="menuInfoPanel_item_type">跳转到网页</span>' +
        '<input type="hidden" name="type" value="view">' +
        '</li>' +
        '<li class="menuInfoPanel_item">' +
        '<div class="menuInfoPanel_item_title">' +
        '</div>' +
        '<div class="menuInfoPanel_item_content">' +
        '<input type="text" name="url" class="menuInfoPanel_item_url">' +
        '</div>' +
        '</li>' +
        '</ul>' +
        '</div>' +
        '</div>' +
        '</div>';
    $btnAddSubMenu.click(function () {
        $mainMenuContainer.hide();
        $(this).before(subMenuHtml);
        if ($(".subMenu_container").length >= 5) {
            $(this).css('display','none');
        }
    });
    // 删除子菜单
    $(document).on('click', '.del_subMenu', function () {
        $(this).parents('.subMenu_container').remove();
        $btnAddSubMenu.css('display','');
    })
});