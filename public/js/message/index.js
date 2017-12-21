
page.initSelect2({
    templateResult: page.formatStateImg,
    templateSelection: page.formatStateImg
});
var $message = $('#message');
var $objects = $('#objects');
var $imageText = $('#imagetext');
var $addAttachment = $('#add-attachment');
var $saveAttachment = $('#save-attachment');
var $cancelAttachment = $('#cancel-attachment');
var $addImageText = $('#add-imagetext');
var $saveImageText = $('#save-imagetext');
var $cancelImageText = $('#cancel-imagetext');
var $send = $('#send');

// 附件管理
$addAttachment.on('click', function() {
    $message.hide();
    $objects.show();
});
$saveAttachment.on('click', function() {});
$cancelAttachment.on('click',function () {
    $message.show();
    $objects.hide();
});

// 图文管理
$addImageText.on('click', function() {
    $message.hide();
    $imageText.show();
});
$saveImageText.on('click', function() {

});
$cancelImageText.on('click',function () {
    $message.show();
    $imageText.hide();
});

//部门树以及联系人加载
if (typeof contacts === 'undefined') {
    $.getMultiScripts(['js/message/contacts.tree.js'], page.siteRoot())
        .done(function() { contacts.init('messages/index'); });
} else { contacts.init('messages/index'); }

$send.on('click', function() {
    var appIds = $('#app_ids').val();
    var selectedDepartmentIds = $('#selectedDepartmentIds').val();
    var content = $('#messageText').val();
    // if (appIds.toString() === '') {
    //     alert('应用不能为空');
    //     return false
    // }
    // if (selectedDepartmentIds === '') {
    //     alert('对象不能为空');
    //     return false
    // }
    // if (content === '') {
    //     alert('内容不能为空');
    //     return false
    // }
    $.ajax({
        url: page.siteRoot() + "messages/store",
        type: 'POST',
        dataType: 'json',
        data:  {
            app_ids: appIds,
            departIds: selectedDepartmentIds,
            content: content,
            _token: $('#csrf_token').attr('content')},

        success: function (result) {
            if (result.error !== 0) {
                page.inform("操作成功",result.message, page.success);
            }
        },
        error: function (result) {
            console.log(result);
            page.inform("操作失败",result.message, page.failure);

        }
    });
});
