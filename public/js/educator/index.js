page.index('educators');

/** 导出excel 选择学校 */
var item = 'educators/';
var type = 'export';
if (typeof custodian === 'undefined') {
    $.getMultiScripts(['js/custodian.relationship.js'], page.siteRoot())
        .done(function() { custodian.init(item, type, ''); });
} else { custodian.init(item, type, ''); }

/**
 * 批量导入教职工
 * @type {Mixed|jQuery|HTMLElement}
 */
var $import = $('#import');
var $importEducator = $('#import-educator');
var $file = $('#confirm-import');
$import.on('click', function () {
    $importEducator.modal({backdrop: true});
    $file.on('click', function () {
        page.inform("温馨提示", '正在导入中...', page.info);
        var formData = new FormData();
        formData.append('file', $('#fileupload')[0].files[0]);
        formData.append('_token', $('#csrf_token').attr('content'));
        $.ajax({
            url: "../educators/import",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (result) {
                if (result.error !== 0) {
                    page.inform("操作失败",result.message, page.failure);
                }
            },
            error: function (result) {
                console.log(result);
                page.inform("操作失败",result.message, page.failure);

            }
        });
    })
});