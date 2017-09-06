CKEDITOR.replace('content');
// Select2
$('select').select2();
// Switchery
Switcher.init();
// Cancel button
$('#cancel, #record-list').on('click', function() {
    var $activeTabPane = $('#tab_' + page.getActiveTabId());
    page.getTabContent($activeTabPane, page.siteRoot() + 'wsm_articles/index')
});
// Parsley
var $form = $('#formWsmArticle');
// 处理CKEDITOR的值
function CKupdate() {
    for (instance in CKEDITOR.instances)
        CKEDITOR.instances[instance].updateElement();
}
$form.parsley().on("form:validated", function () {
    if ($('.parsley-error').length === 0) {
        //在提交表单前需要做以上处理
        CKupdate();
        $.ajax({
            type: 'PUT',
            dataType: 'json',
            url: page.siteRoot() + 'wsm_articles/update/' + $('#id').val(),
            data: $form.serialize(),
            success: function(result) {
                page.inform(
                    '操作结果', result.message,
                    result.statusCode === 200 ? page.success : page.failure
                );
            },
            error: function(e) {
                var obj = JSON.parse(e.responseText);
                page.inform('出现异常', obj['message'], page.failure);
            }

        });
    }
}).on('form:submit', function() {
    return false;
});

// $('textarea.ckeditor').each(function () {
//     var $textarea = $(this);
//     alert(CKEDITOR.instances[$textarea.attr('name')].getData());
//     // $textarea.val(CKEDITOR.instances[$textarea.attr('name')].getData());
// });
var $pre = $('.preview');
var $uploadFile = $('#uploadFile');
// 初始化
$uploadFile.fileinput({
    language: 'zh',
    theme: 'explorer',
    uploadUrl: "../../wap_sites/uploadImages",
    uploadAsync: false,
    maxFileCount: 5,
    minImageWidth: 50, //图片的最小宽度
    minImageHeight: 50,//图片的最小高度
    maxImageWidth: 1000,//图片的最大宽度
    maxImageHeight: 1000,//图片的最大高度
    allowedFileExtensions: ['jpg', 'gif', 'png'],//接收的文件后缀
    fileActionSettings: {
        showRemove: true,
        showUpload: false,
        showDrag: false
    },
    uploadExtraData: {
        '_token': $('#csrf_token').attr('content')
    }
});
// 上传成功
$uploadFile.on("filebatchuploadsuccess", function (event, data, previewId, index) {
    // 填充数据
    var response = data.response.data;
    $.each(response, function (index, obj) {
        $pre.append('<div class="img-item"><img src="../../../' + obj.path + '" id="' + obj.id + '"><div class="del-mask"><i class="delete glyphicon glyphicon-trash"></i></div></div>');
        $pre.append('<input type="hidden" name="media_ids[]" value="' + obj.id + '">');
    });
    // 成功后关闭弹窗
    setTimeout(function () {
        $('#modalPic').modal('hide');
    }, 800)
});

// modal关闭，内容清空
$('#modalPic').on('hide.bs.modal', function () {
    $uploadFile.fileinput('clear');
});
// 点击删除按钮
$('body').on('click', '.delete', function () {
    $(this).parent().parent().remove();
    $pre.append('<input type="hidden" name="del_ids[]" value="' + $(this).parent().siblings().attr('id') + '">');
});
